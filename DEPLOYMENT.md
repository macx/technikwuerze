# Production Deployment (Tag-based Releases + rsync)

This project uses a split deployment model:

- `main` repository: code, templates, assets, config, vendor, dist
- `content` repository: Kirby content (`content/`), maintained separately in production

## CI/CD Architecture

1. `CI` workflow runs on PR/push (`main`, `develop`):
- install dependencies
- run tests/checks
- build assets
- validate composer config

2. `Create Release Tag` workflow runs manually:
- validates SemVer input (`1.4.0`)
- runs checks/build
- updates `package.json` version
- commits release bump to `main`
- creates/pushes tag (`v1.4.0`)
- creates GitHub Release

3. `Deploy From Tag` workflow runs only on `v*` tags:
- runs checks/build again
- installs production Composer dependencies
- deploys via `rsync` to production server
- clears Kirby cache on server

`content/`, `media/`, accounts, cache and sessions are excluded from `rsync` via `.rsyncignore`.

## Required GitHub Secrets

Use a dedicated deploy SSH user with least privilege (target path only, e.g. `html`).

Configure in `Repository -> Settings -> Secrets and variables -> Actions`:

- `DEPLOY_SSH_KEY` (private key used by Actions for server SSH)
- `DEPLOY_HOST` (hostname/IP)
- `DEPLOY_USER` (SSH user)
- `DEPLOY_PATH` (absolute project path on server)
- `DEPLOY_PORT` (optional, defaults to `22`)
- `RELEASE_TOKEN` (PAT/Fine-grained token with `contents:write` for tag + version-bump push)

## First-Time Server Setup

You can run the helper script on server:

```bash
DEPLOY_PATH=/var/www/technikwuerze \
CONTENT_REPO=git@github.com:macx/technikwuerze-content.git \
bash ops/bootstrap-production.sh
```

Or execute manually:

1. Deploy user and directory:
```bash
sudo adduser deploy
sudo mkdir -p /var/www/technikwuerze
sudo chown -R deploy:deploy /var/www/technikwuerze
```

2. Add GitHub Actions public key to server:
```bash
mkdir -p ~/.ssh
chmod 700 ~/.ssh
cat >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
```

3. Initial code bootstrap (one-time, before first workflow run):
```bash
cd /var/www/technikwuerze
# optional: copy current project structure once, workflow will overwrite code files later
```

4. Content repository setup (mandatory, separate lifecycle):
```bash
cd /var/www/technikwuerze
git clone git@github.com:macx/technikwuerze-content.git content
```

5. Runtime folders:
```bash
mkdir -p content/.db content/audio site/cache site/sessions site/accounts
```

6. PHP/webserver permissions: ensure webserver can write to:
- `content/`
- `media/`
- `site/cache/`
- `site/sessions/`

## Content Repo in Production

Production edits happen in Kirby Panel and are pushed by `thathoff/kirby-git-content`.

Expected production config (`site/config/config.production.php`):
- `thathoff.git-content.commit => true`
- `thathoff.git-content.push => true`
- `thathoff.git-content.pull => false`
- `thathoff.git-content.branch => main`

## Audio and Database Runtime Policy

- Central audio storage: `content/audio/`
- Episode references use file UUID (`file://...`), not local episode MP3 copies
- Runtime sqlite DBs in `content/.db/`:
  - `komments.sqlite`
  - podcaster stats sqlite
- Binary/runtime files are not versioned in Git; sync with `rsync` when needed.

## Release Trigger

Use GitHub Actions `Create Release Tag` workflow and pass `version` without prefix, for example:
- `1.4.0` (creates tag `v1.4.0`)

Manual fallback from local machine:

```bash
DEPLOY_HOST=example.org DEPLOY_USER=deploy DEPLOY_PATH=/var/www/technikwuerze \
bash ops/deploy-manual-rsync.sh
```

## Rollback

1. Pick a previous GitHub release artifact.
2. Extract and deploy via `rsync` to `DEPLOY_PATH` (same excludes/rules).
3. Clear `site/cache/*`.

## Post-Deploy Validation

- Homepage loads
- `/panel` login works
- podcast episode page + player works
- comments visible
- production `content/.git` exists and points to content repo
- production writes to `content/.db` and `media`

## Notes

- Never deploy `content/` from the main repo pipeline.
- Keep `.rsyncignore` as the single source of truth for deployment excludes.
