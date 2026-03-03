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

mkdir -p content/.db content/audio content/covers

pull_db() {
  rsync -avz --delete -e "${RSYNC_SSH[*]}" \
    "${SYNC_USER}@${SYNC_HOST}:${REMOTE_CONTENT_PATH}/.db/" \
    "./content/.db/"
}

pull_audio() {
  rsync -avz --delete -e "${RSYNC_SSH[*]}" \
    "${SYNC_USER}@${SYNC_HOST}:${REMOTE_CONTENT_PATH}/audio/" \
    "./content/audio/"
}

pull_covers() {
  rsync -avz --delete -e "${RSYNC_SSH[*]}" \
    "${SYNC_USER}@${SYNC_HOST}:${REMOTE_CONTENT_PATH}/covers/" \
    "./content/covers/"
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
  *)
    echo "Unknown mode: $MODE (use: db|audio|covers)" >&2
    exit 1
    ;;
esac

printf 'Done: pulled %s from %s\n' "$MODE" "$SYNC_HOST"
