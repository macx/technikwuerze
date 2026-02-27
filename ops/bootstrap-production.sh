#!/usr/bin/env bash
set -euo pipefail

if [[ "${EUID}" -eq 0 ]]; then
  echo "Run as deploy user, not as root." >&2
  exit 1
fi

DEPLOY_PATH="${DEPLOY_PATH:-/var/www/technikwuerze}"
CONTENT_REPO="${CONTENT_REPO:-git@github.com:macx/technikwuerze-content.git}"

mkdir -p "${DEPLOY_PATH}"
cd "${DEPLOY_PATH}"

mkdir -p site/cache site/sessions site/accounts media content/.db content/audio

if [[ ! -d content/.git ]]; then
  rm -rf content
  git clone "${CONTENT_REPO}" content
fi

mkdir -p content/.db content/audio
[[ -f content/.db/.gitkeep ]] || touch content/.db/.gitkeep
[[ -f content/audio/.gitkeep ]] || touch content/audio/.gitkeep

echo "Bootstrap completed at ${DEPLOY_PATH}."
echo "Next steps:"
echo "1) Ensure webserver write permissions for content/, media/, site/cache/, site/sessions/."
echo "2) Configure GitHub Actions secrets for rsync deploy."
echo "3) Trigger workflow: Release and Deploy."
