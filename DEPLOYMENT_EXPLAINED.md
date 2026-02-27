# Deployment Explained

The project runs two independent lifecycles:

1. **Code lifecycle (main repository)**

- Commit code -> push `main`
- GitHub Actions `CI` runs checks/build
- `Release Please` creates/updates a release PR automatically
- Merging the release PR creates the release tag
- `Deploy From Tag` is triggered by the tag push
- Code is synced to production via `rsync`

2. **Content lifecycle (content repository)**

- Editors change content in production Kirby Panel
- `thathoff/kirby-git-content` commits and pushes to `technikwuerze-content`
- Local/dev syncs content by pulling content repo

Why this split:

- avoids overwriting panel-managed content during code deploy
- keeps runtime files (audio, sqlite) out of Git
- allows safe code releases without content conflicts

## What `rsync` deploys

- templates, snippets, plugins, config
- `dist/` frontend build output
- `vendor/` Composer dependencies

## What `rsync` never deploys

- `content/`
- `media/`
- `site/accounts/`, `site/cache/`, `site/sessions/`
- runtime storage folders

These rules are centralized in `.rsyncignore`.

## Release model

- Versioning is semantic (`vX.Y.Z`).
- `Release Please` creates the GitHub Release entry when the release PR is merged.
- `Deploy From Tag` deploys exactly that tagged revision.

## Practical rollback

- select earlier release artifact
- deploy artifact to server path with same `rsync` rules
- clear `site/cache/*`

## Runtime data strategy

- audio files: `content/audio/`
- sqlite runtime dbs: `content/.db/`
- both synced manually (`rsync`), not via Git
