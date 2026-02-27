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

ssh -p "${DEPLOY_PORT}" "${DEPLOY_USER}@${DEPLOY_HOST}" "cd '${DEPLOY_PATH}' && rm -rf site/cache/* || true"

echo "Manual rsync deploy completed."
