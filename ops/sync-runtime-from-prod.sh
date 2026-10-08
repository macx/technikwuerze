#!/usr/bin/env bash
set -euo pipefail

MODE="${1:-db}"

# Auto-load local env vars for sync commands
if [[ -f .env ]]; then
  set -a
  # shellcheck disable=SC1091
  source .env
  set +a
fi

if [[ -f .env.local ]]; then
  set -a
  # shellcheck disable=SC1091
  source .env.local
  set +a
fi

SYNC_HOST="${SYNC_HOST:-${DEPLOY_HOST:-}}"
SYNC_USER="${SYNC_USER:-${DEPLOY_USER:-}}"
SYNC_PORT="${SYNC_PORT:-${DEPLOY_PORT:-22}}"
SYNC_REMOTE_PROJECT_PATH="${SYNC_REMOTE_PROJECT_PATH:-${DEPLOY_PATH:-}}"

if [[ -z "$SYNC_HOST" || -z "$SYNC_USER" || -z "$SYNC_REMOTE_PROJECT_PATH" ]]; then
  echo "Missing config. Set SYNC_HOST, SYNC_USER, SYNC_REMOTE_PROJECT_PATH (or DEPLOY_* equivalents)." >&2
  exit 1
fi

REMOTE_CONTENT_PATH="${SYNC_REMOTE_PROJECT_PATH%/}/content"
RSYNC_SSH=(ssh -p "$SYNC_PORT")

mkdir -p content/.db content/audio content/covers content/avatars

ask_yes_no() {
  local question="$1"
  local reply

  while true; do
    printf '%s [y/n] ' "$question"
    if ! read -r reply; then
      echo "No input, aborting." >&2
      exit 1
    fi
    case "$reply" in
      y|Y|yes|j|J|ja) return 0 ;;
      n|N|no|nein) return 1 ;;
      *) echo "Please answer y or n." ;;
    esac
  done
}

pull_dir() {
  local remote_path="$1"
  local local_path="$2"
  local deletions

  printf 'Pull (--delete): %s -> %s\n' "$remote_path" "$local_path"

  if ! ask_yes_no "Did you already PUSH your local changes for this data to production (if you have any)?"; then
    echo "Aborted. Push first, otherwise local-only files are lost."
    exit 1
  fi

  deletions="$(rsync -a --delete --dry-run --itemize-changes -e "${RSYNC_SSH[*]}" "$remote_path" "$local_path" | grep '^\*deleting' || true)"

  if [[ -n "$deletions" ]]; then
    printf 'These local files do not exist on production and will be DELETED:\n%s\n' "$deletions"
  fi

  if ! ask_yes_no "Really overwrite the local data with the production data?"; then
    echo "Aborted."
    exit 1
  fi

  rsync -avz --delete -e "${RSYNC_SSH[*]}" "$remote_path" "$local_path"
}

pull_db() {
  pull_dir "${SYNC_USER}@${SYNC_HOST}:${REMOTE_CONTENT_PATH}/.db/" "./content/.db/"
}

pull_audio() {
  pull_dir "${SYNC_USER}@${SYNC_HOST}:${REMOTE_CONTENT_PATH}/audio/" "./content/audio/"
}

pull_covers() {
  pull_dir "${SYNC_USER}@${SYNC_HOST}:${REMOTE_CONTENT_PATH}/covers/" "./content/covers/"
}

pull_avatars() {
  pull_dir "${SYNC_USER}@${SYNC_HOST}:${REMOTE_CONTENT_PATH}/avatars/" "./content/avatars/"
}

pull_accounts() {
  pull_dir "${SYNC_USER}@${SYNC_HOST}:${SYNC_REMOTE_PROJECT_PATH%/}/site/accounts/" "./site/accounts/"
}

case "$MODE" in
  db)
    pull_db
    ;;
  audio)
    pull_audio
    ;;
  covers)
    pull_covers
    ;;
  avatars)
    pull_avatars
    ;;
  accounts)
    pull_accounts
    ;;
  *)
    echo "Unknown mode: $MODE (use: db|audio|covers|avatars|accounts)" >&2
    exit 1
    ;;
esac

printf 'Done: pulled %s from %s\n' "$MODE" "$SYNC_HOST"
