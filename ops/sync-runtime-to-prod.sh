#!/usr/bin/env bash
set -euo pipefail

MODE="${1:-all}"

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

ssh -p "$SYNC_PORT" "${SYNC_USER}@${SYNC_HOST}" "mkdir -p '${REMOTE_CONTENT_PATH}/.db' '${REMOTE_CONTENT_PATH}/audio'"

push_db() {
  rsync -avz --delete -e "${RSYNC_SSH[*]}" \
    "./content/.db/" \
    "${SYNC_USER}@${SYNC_HOST}:${REMOTE_CONTENT_PATH}/.db/"
}

push_comments() {
  if [[ ! -f "./content/.db/komments.sqlite" ]]; then
    echo "Missing local file: ./content/.db/komments.sqlite" >&2
    exit 1
  fi

  rsync -avz -e "${RSYNC_SSH[*]}" \
    "./content/.db/komments.sqlite" \
    "${SYNC_USER}@${SYNC_HOST}:${REMOTE_CONTENT_PATH}/.db/komments.sqlite"
}

push_audio() {
  rsync -avz --delete -e "${RSYNC_SSH[*]}" \
    "./content/audio/" \
    "${SYNC_USER}@${SYNC_HOST}:${REMOTE_CONTENT_PATH}/audio/"
}

case "$MODE" in
  comments)
    push_comments
    ;;
  db)
    push_db
    ;;
  audio)
    push_audio
    ;;
  all|runtime)
    push_comments
    push_audio
    ;;
  *)
    echo "Unknown mode: $MODE (use: comments|db|audio|all)" >&2
    exit 1
    ;;
esac

printf 'Done: pushed %s to %s\n' "$MODE" "$SYNC_HOST"
