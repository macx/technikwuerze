# Deployment Quick Reference

## Workflows

- `CI`: checks pull requests to `main` and pushes to `main`
- `release-it`: local release command (version bump + commit + tag push)
- `Deploy From Tag`: production `rsync` on tag push (`v*`)

## Required Secrets

- `DEPLOY_SSH_KEY`
- `DEPLOY_HOST`
- `DEPLOY_USER`
- `DEPLOY_PATH`
- `DEPLOY_PORT` (optional)

## Important Excludes (`.rsyncignore`)

- `content/`
- `media/`
- `site/accounts/`
- `site/cache/`
- `site/sessions/`
- `storage/`
- `.htpasswd` (including `public/.htpasswd`)

## Content Flow

- Production panel edit -> git-content auto-commit/push -> content repo
- Local machine sync: `cd content && git pull origin main`

## Binary/Runtime Sync (manual via rsync)

```bash
# production -> local
rsync -avz user@host:/var/www/technikwuerze/content/.db/ ./content/.db/
rsync -avz user@host:/var/www/technikwuerze/content/audio/ ./content/audio/
rsync -avz user@host:/var/www/technikwuerze/content/avatars/ ./content/avatars/

# local -> production (only if needed)
rsync -avz ./content/audio/ user@host:/var/www/technikwuerze/content/audio/
rsync -avz ./content/avatars/ user@host:/var/www/technikwuerze/content/avatars/
```

## Release Tags

- format: `vX.Y.Z` (or `technikwuerze-vX.Y.Z`)
- created by `release-it` and pushed from local

## Podcaster Update Checklist (Pinned + Patch)

1. Keep `mauricerenck/podcaster` pinned to an exact version in `composer.json`.
2. If updating Podcaster, update the pinned version intentionally (no broad constraints).
3. Rebase/adjust `patches/podcaster-central-audio-feed.patch` against the new upstream file.
4. Run `composer update mauricerenck/podcaster cweagans/composer-patches`.
5. Confirm patch apply in Composer output and verify `site/plugins/podcaster/lib/Podcast.php` contains the `getAudioFile()` based filter.
6. Run tests/checks and validate feed output before tagging a release.

## Feed Smoke Test

Use this after local setup (`php` server running) and after production deploy.

```bash
# Local feed item count (should be > 0)
. ./.env 2>/dev/null || true
[ -n "$DEV_HOST" ] || DEV_HOST=127.0.0.1
[ -n "$DEV_PHP_PORT" ] || DEV_PHP_PORT=8000
curl -fsSL "http://$DEV_HOST:$DEV_PHP_PORT/mediathek/feed" | rg -c "<item>"

# Production feed item count (replace host)
curl -fsSL "https://technikwuerze.de/mediathek/feed" | rg -c "<item>"
```

Expected result:

- command exits with status `0`
- item count is greater than `0`
