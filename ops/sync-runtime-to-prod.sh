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
mkdir -p content/covers

ssh -p "$SYNC_PORT" "${SYNC_USER}@${SYNC_HOST}" "mkdir -p '${REMOTE_CONTENT_PATH}/.db' '${REMOTE_CONTENT_PATH}/audio' '${REMOTE_CONTENT_PATH}/covers'"

confirm_overwrite() {
  local source_path="$1"
  local target_path="$2"

  printf 'This will overwrite remote data via rsync (--delete):\n  %s -> %s\nContinue? [y/N] ' "$source_path" "$target_path"
  read -r reply
  case "$reply" in
    y|Y) ;;
    *)
      echo "Aborted."
      exit 1
      ;;
  esac
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
  *)
    echo "Unknown mode: $MODE (use: db|audio|covers)" >&2
    exit 1
    ;;
esac

printf 'Done: pushed %s to %s\n' "$MODE" "$SYNC_HOST"
