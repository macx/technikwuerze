# Deployment Quick Reference

## Workflows

- `CI`: checks PR/push (`main`, `develop`)
- `Release Please`: auto-creates/updates release PR after merges to `main`
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

## Content Flow

- Production panel edit -> git-content auto-commit/push -> content repo
- Local machine sync: `cd content && git pull origin main`

## Binary/Runtime Sync (manual via rsync)

```bash
# production -> local
rsync -avz user@host:/var/www/technikwuerze/content/.db/ ./content/.db/
rsync -avz user@host:/var/www/technikwuerze/content/audio/ ./content/audio/

# local -> production (only if needed)
rsync -avz ./content/audio/ user@host:/var/www/technikwuerze/content/audio/
```

## Release Tags

- format: `vX.Y.Z` (or `technikwuerze-vX.Y.Z`)
- created when the `Release Please` PR is merged
