#!/usr/bin/env bash
set -euo pipefail

# Rebuilds the production search index (full rebuild, ~1 minute). Run after content pulls that add or
# change episodes; Panel edits on production update the index through hooks on their own.

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

ssh -p "$SYNC_PORT" "${SYNC_USER}@${SYNC_HOST}" \
  "cd '${SYNC_REMOTE_PROJECT_PATH}' && TW_BASE='${SYNC_REMOTE_PROJECT_PATH}' TW_FORCE=1 php -d error_reporting=0 -d max_execution_time=0 -d memory_limit=2G" \
  < ops/reindex-search.php
