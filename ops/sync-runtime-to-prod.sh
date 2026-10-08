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

mkdir -p content/.db content/audio
mkdir -p content/covers content/avatars

ssh -n -p "$SYNC_PORT" "${SYNC_USER}@${SYNC_HOST}" "mkdir -p '${REMOTE_CONTENT_PATH}/.db' '${REMOTE_CONTENT_PATH}/audio' '${REMOTE_CONTENT_PATH}/covers' '${REMOTE_CONTENT_PATH}/avatars'"

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

confirm_overwrite() {
  local source_path="$1"
  local target_path="$2"

  printf 'Push (--delete): %s -> %s\n' "$source_path" "$target_path"

  if ! ask_yes_no "Did you PULL from production right before editing locally?"; then
    echo "Aborted. Pull first, otherwise changes made on production since then are lost."
    exit 1
  fi

  if ! ask_yes_no "Really overwrite production with the local data?"; then
    echo "Aborted."
    exit 1
  fi
}

push_db() {
  rsync -avz --delete -e "${RSYNC_SSH[*]}" \
    "./content/.db/" \
    "${SYNC_USER}@${SYNC_HOST}:${REMOTE_CONTENT_PATH}/.db/"
}

push_audio() {
  rsync -avz --delete -e "${RSYNC_SSH[*]}" \
    "./content/audio/" \
    "${SYNC_USER}@${SYNC_HOST}:${REMOTE_CONTENT_PATH}/audio/"
}

push_covers() {
  rsync -avz --delete -e "${RSYNC_SSH[*]}" \
    "./content/covers/" \
    "${SYNC_USER}@${SYNC_HOST}:${REMOTE_CONTENT_PATH}/covers/"
}

push_avatars() {
  rsync -avz --delete -e "${RSYNC_SSH[*]}" \
    "./content/avatars/" \
    "${SYNC_USER}@${SYNC_HOST}:${REMOTE_CONTENT_PATH}/avatars/"
}

case "$MODE" in
  db)
    confirm_overwrite "./content/.db/" "${SYNC_USER}@${SYNC_HOST}:${REMOTE_CONTENT_PATH}/.db/"
    push_db
    ;;
  audio)
    confirm_overwrite "./content/audio/" "${SYNC_USER}@${SYNC_HOST}:${REMOTE_CONTENT_PATH}/audio/"
    push_audio
    ;;
  covers)
    confirm_overwrite "./content/covers/" "${SYNC_USER}@${SYNC_HOST}:${REMOTE_CONTENT_PATH}/covers/"
    push_covers
    ;;
  avatars)
    confirm_overwrite "./content/avatars/" "${SYNC_USER}@${SYNC_HOST}:${REMOTE_CONTENT_PATH}/avatars/"
    push_avatars
    ;;
  *)
    echo "Unknown mode: $MODE (use: db|audio|covers|avatars)" >&2
    exit 1
    ;;
esac

printf 'Done: pushed %s to %s\n' "$MODE" "$SYNC_HOST"
