# AI Agent Operating Guide (Technikwürze)

This file defines how AI coding agents (Codex, Gemini, Claude, etc.) should work in this repository.

## 1) Primary Goal

Maintain and evolve the Technikwürze Kirby site safely and consistently:

- keep runtime stable (Kirby + plugins),
- keep content workflows reproducible,
- avoid destructive or hard-to-review changes,
- prefer small, testable, reversible edits.

## 2) Stack Overview

- CMS: Kirby 5 (`getkirby/cms`)
- Frontend tooling: Vite 5 + TypeScript (`vite-plugin-kirby`)
- Main plugins:
  - `mauricerenck/podcaster` (podcast feed/player/stats)
  - `mauricerenck/komments` (comments)
  - `thathoff/kirby-git-content` (content Git integration)
- Repositories:
  - Main repo: code/templates/assets/config
  - `content/` is a separate Git repository

## 3) Repository Boundaries

- Main repo must not depend on uncommitted content-side runtime state.
- Content repo is authoritative for Kirby content files.
- Binary runtime data (audio files, sqlite DB files) is synchronized by `rsync`, not Git.

## 4) Data Storage Rules (Important)

### Audio

- Audio is centralized in `content/audio/`.
- Episodes reference audio via file UUID in `Podcasteraudio`:
  - `- file://<uuid>`
- Do not copy MP3 files into each episode directory.
- Keep `content/audio/.gitkeep` present.

### Databases

- Active sqlite runtime path is `content/.db/`.
- Komments DB: `content/.db/komments.sqlite`
- Podcaster stats sqlite path is configured to `content/.db/` in:
  - `site/config/config.php`
  - `site/config/config.production.php`
- Do not version sqlite binaries.

### Gitignore intent

- Main repo ignores local sqlite artifacts (including `/.sqlite/`).
- Content repo ignores `*.sqlite`, `*.db`, audio/video binaries.
- Keep placeholders like `.gitkeep` tracked where needed.

## 5) Language & Content Conventions

- Content text should use proper German umlauts (`ä`, `ö`, `ü`, `Ä`, `Ö`, `Ü`, `ß`) where linguistically correct.
- Do not alter technical identifiers when normalizing language:
  - do not touch UUIDs, `file://...`, `user://...`, slugs, URLs.
- Panel labels should be bilingual where already established (German + English).

## 6) Participant Model (Current State)

- Participants are pages under `content/3_teilnehmende`.
- Public visibility is controlled by native Kirby status:
  - `listed` = public
  - `unlisted/draft` = not public
- Public participant listing page uses one HTML list, CSS columns.
- Participant detail page includes computed participation stats from episode host/guest assignments.

## 7) Podcast/Episode Model (Current State)

- Episodes live under `content/2_mediathek/staffel-*/...`.
- Hosts/Guests are assigned via participant page references.
- Audio field in episode panel is configured to select/upload from central `site.find("audio")`.

## 8) Migration Workspace Policy

- One-off migration artifacts are under `migration/`:
  - `migration/data/`
  - `migration/scripts/`
  - `migration/reports/`
- `migration/.gitignore` intentionally prevents tracking of heavy/temporary artifacts.
- If script paths are changed, keep them runnable from project root:
  - `php migration/scripts/<script>.php ...`

## 9) Editing Rules for Agents

- Never run destructive git commands (`reset --hard`, etc.) unless explicitly requested.
- Do not revert user changes you did not create.
- Prefer minimal diffs and maintain existing style.
- For bulk transforms, add safeguards and verify with spot checks.
- Run Prettier after edits on touched files before finishing work:
  - `pnpm exec prettier --write <files...>`
  - or `pnpm run format` for broader sweeps when appropriate.
- If changing content at scale:
  - protect technical fields/references,
  - run pattern checks before/after.

## 10) Validation Checklist Before Finishing

- Syntax:
  - `php -l` for edited PHP files
- Config:
  - verify relevant paths/options after changes
- Content-safe transforms:
  - sample-check at least one episode and one participant file
- Ignore rules:
  - verify with `git check-ignore -v <path>`
- If migration scripts touched:
  - ensure path constants and usage examples are still correct

## 11) Safe Defaults When Uncertain

- Ask before irreversible bulk content rewrites.
- Prefer adding docs over implicit behavior.
- Prefer runtime-safe fallback over breaking UX.
- Keep production behavior explicit in config and docs.

## 12) Production Delivery Protocol (Mandatory)

### CI/CD baseline

- Keep three workflows:
  - `CI` (`.github/workflows/test.yml`) for PR/push checks
  - `release-it` (local CLI) for version bump + tag creation
  - `Deploy From Tag` (`.github/workflows/deploy.yml`) for production rollouts from tags only
- Use Corepack-managed pnpm from `package.json` (`packageManager`) in CI.
- Keep `pnpm-lock.yaml` versioned; CI uses `pnpm install --frozen-lockfile`.
- Production deployment method is `rsync` over SSH.
- Deployment excludes are centralized in `.rsyncignore` (single source of truth).

### Release policy

- Releases are semantic tags (`vX.Y.Z`).
- `release-it` creates the version commit and release tag from `develop`.
- Deployments run only when a release tag is pushed (`v*`, `technikwuerze-v*`).

### Server model

- Webserver document root in production must point to `public/` (not repository root).
- `content/` is a dedicated Git repository on production and must exist as `content/.git`.
- Main code deployment must never overwrite `content/`, `media/`, accounts, cache or sessions.
- Runtime binaries/state are not in Git:
  - `content/audio/` (audio files)
  - `content/.db/*.sqlite` (komments + podcaster stats)
- Runtime binaries/state are synchronized manually via `rsync` when needed.

### Agent behavior for deployment changes

- If deployment paths/secrets/excludes change, update all of:
  - workflow files,
  - deployment docs,
  - this `AGENTS.md`.
- Never introduce a deploy step that writes `content/` from main repo CI.
- Never use `rsync --delete-excluded` in deploy flows. With `content/` in excludes, this can delete production content.
- Keep a deploy preflight check that requires `${DEPLOY_PATH}/content/.git` to exist before any rsync runs.
- Validate workflow YAML syntax and run a local sanity check of referenced paths.
- Keep `ops/bootstrap-production.sh` and `ops/deploy-manual-rsync.sh` aligned with docs/workflows.
- Prefer a dedicated deploy SSH user with write access limited to the deployment target (`html`) only.
