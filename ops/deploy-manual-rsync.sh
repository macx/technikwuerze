#!/usr/bin/env bash
set -euo pipefail

: "${DEPLOY_HOST:?Set DEPLOY_HOST}"
: "${DEPLOY_USER:?Set DEPLOY_USER}"
: "${DEPLOY_PATH:?Set DEPLOY_PATH}"

DEPLOY_PORT="${DEPLOY_PORT:-22}"

# Safety preflight: never deploy to a target without separate content repository
ssh -p "${DEPLOY_PORT}" "${DEPLOY_USER}@${DEPLOY_HOST}" \
  "test -d '${DEPLOY_PATH}/content/.git'" \
  || { echo "ERROR: ${DEPLOY_PATH}/content/.git is missing. Aborting deploy."; exit 1; }

rsync -az --delete \
  --exclude-from='.rsyncignore' \
  -e "ssh -p ${DEPLOY_PORT}" \
  ./ "${DEPLOY_USER}@${DEPLOY_HOST}:${DEPLOY_PATH}/"

ssh -p "${DEPLOY_PORT}" "${DEPLOY_USER}@${DEPLOY_HOST}" "cd '${DEPLOY_PATH}' && find site/cache -mindepth 1 -maxdepth 1 ! -name 'twz-search' ! -name 'twz-search-meta.json' -exec rm -rf {} + || true"

ssh -p "${DEPLOY_PORT}" "${DEPLOY_USER}@${DEPLOY_HOST}" \
  "cd '${DEPLOY_PATH}' && TW_BASE='${DEPLOY_PATH}' php -d error_reporting=0 -d max_execution_time=0 -d memory_limit=2G" \
  < ops/reindex-search.php || echo "WARNING: search index rebuild failed; run 'pnpm run search:reindex-production'."

echo "Manual rsync deploy completed."
