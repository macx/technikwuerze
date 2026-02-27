#!/usr/bin/env bash
set -euo pipefail

: "${DEPLOY_HOST:?Set DEPLOY_HOST}"
: "${DEPLOY_USER:?Set DEPLOY_USER}"
: "${DEPLOY_PATH:?Set DEPLOY_PATH}"

DEPLOY_PORT="${DEPLOY_PORT:-22}"

rsync -az --delete --delete-excluded \
  --exclude-from='.rsyncignore' \
  -e "ssh -p ${DEPLOY_PORT}" \
  ./ "${DEPLOY_USER}@${DEPLOY_HOST}:${DEPLOY_PATH}/"

ssh -p "${DEPLOY_PORT}" "${DEPLOY_USER}@${DEPLOY_HOST}" "cd '${DEPLOY_PATH}' && rm -rf site/cache/* || true"

echo "Manual rsync deploy completed."
