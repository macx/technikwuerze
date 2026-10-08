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
- Binary runtime data (audio files, avatar images, sqlite DB files) is synchronized by `rsync`, not Git.

## 4) Data Storage Rules (Important)

### Audio

- Audio is centralized in `content/audio/`.
- Episodes reference audio via file UUID in `Podcasteraudio`:
  - `- file://<uuid>`
- Do not copy MP3 files into each episode directory.
- Keep `content/audio/.gitkeep` present.

### Avatars

- Participant avatars are centralized in `content/avatars/`.
- Participants reference avatars via file UUID in `Profile-image`.
- Keep `content/avatars/.gitkeep` present.

### Databases

- Active sqlite runtime path is `content/.db/`.
- Komments DB: `content/.db/komments.sqlite`
- Podcaster stats sqlite path is configured to `content/.db/` in:
  - `site/config/config.php`
  - `site/config/config.production.php`
- Do not version sqlite binaries.
- We do work local for sqlite edits. Workflow for any change to runtime sqlite data (e.g. `komments.sqlite`):
  1. `pnpm run sync:pull:db` — pull the current production DB into local `content/.db/`.
  2. Make the change locally (migration/backfill/manual fix), verify it.
  3. `pnpm run sync:push:db` — push the updated local DB back to production (`ops/sync-runtime-to-prod.sh db`, requires interactive y/n confirmation, uses `rsync --delete`). Every pull and push asks whether the opposite step was done first; `sync:pull:covers` and `sync:pull:avatars` are separate modes.
     Always pull immediately before editing so the push doesn't clobber production writes that happened since the last pull.

### Gitignore intent

- Main repo ignores local sqlite artifacts (including `/.sqlite/`).
- Content repo ignores `*.sqlite`, `*.db`, audio/video binaries and avatar binaries in `content/avatars/`.
- Keep placeholders like `.gitkeep` tracked where needed.

### Transcripts

- Every episode can carry a `tw-transcript` block as its first block (speaker, timestamp, text segments). Create and update it only through the skills `transkript-import` and `transkript-metadaten` (`.claude/skills/`), which use the versioned toolkit in `scripts/transcripts/` (see its README); never write throwaway helper scripts for this.
- Word-level archives (timestamps, raw speaker ids, id→label mapping) live in `content/.transcripts/tw<N>.json.gz` in the content repo (Kirby ignores dot folders, like `content/.db`) so they can be reused for subtitles, a synced player or search. Raw API responses, intermediate transcripts, venv, logs and draft backups stay in the git-ignored `.work/` (API keys in the git-ignored `.env`); `.work/` is excluded from deployments via `.rsyncignore`. `migration/` is not used for transcripts.
- „Mitlesen“ (word highlight in the transcript while playing) lives only in project code, never in the `kirby-tw-transcript` plugin (it must stay updatable): route `<episode>/transcript-words` (`site/plugins/technikwuerze/lib/transcript-words.php`, `extensions/routes.php`) serves the word archive `content/.transcripts/tw<N>.json.gz` as compact `{t: [ms], w: [word]}`; `src/scripts/components/transcript-words.ts` (+ `transcript-words-align.ts`, unit-tested) aligns page tokens with archive words per segment and marks the current word via the CSS Custom Highlight API (`::highlight(twt-word)`, styled with `--clr-mark`). The button is only injected when `data-transcript-words` is present on the transcript section, i.e. when an archive exists. The transcript heading „Transkript“ is rendered as „Transkript der Folge“ in `site/templates/episode.php` without touching content.
- Site search (Loupe, `site/plugins/technikwuerze/lib/site-search.php`) indexes only the human text of blocks (`twSearchBlocksText`: transcript segment texts and intro, no speaker names, timestamps, toggles or block ids/types; the index stores page paths (`uri()`), never absolute URLs, so results link to the host serving the request (the reindex script may run with the production host); participants contribute only their description as text, the name is the title and the profession the subtitle; keys in `TW_SEARCH_SKIPPED_BLOCK_KEYS` are skipped). When the indexed text or format changes, bump `TW_SEARCH_META_VERSION`. A full rebuild takes about a minute and exceeds PHP's 30 s web limit, so it runs outside the request via `ops/reindex-search.php` (piped to `php` on the server): the deploy keeps `site/cache/twz-search*` when it clears the cache and runs the script after the rsync, which rebuilds only if the index is missing or outdated (a version bump). Panel edits on production update single pages through hooks; after a content pull that adds or changes episodes run `pnpm run search:reindex` (forced full rebuild).
- ElevenLabs credits are paid by David: never buy credits, and stop on `quota_exceeded`. The local Whisper fallback is free but needs proofreading.
- Run `scripts/transcripts/verify.py <N>` after every import (quality gate: speakers, participants, timestamps, language, known misspellings). All published episodes with audio have a transcript (as of 2026-10-04); a newly produced episode gets one through the skill `transkript-import`.

### Kirby User Accounts

- `site/accounts/` is never versioned or synced (gitignored, excluded from `.rsyncignore`) — each environment (local, production) manages its own accounts independently.
- Content (feed page `Podcasterauthor`/`Podcasterowner`, episode `Podcasterauthor`, participant `Linked-user`) references accounts via `user://<uuid>`. Because content IS shared between environments via the content repo, any such UUID must resolve to an account with the same identity in every environment, or the reference silently resolves to nothing (e.g. missing `<itunes:author>`/`<itunes:owner>` in the RSS feed).
- Canonical account: UUID `qwP3CCVv` = David Eiken (`realmacx@gmail.com`), the podcast's show identity, linked from `content/3_teilnehmende/14_david-eiken/participant.txt` via `Linked-user`. Any environment must provision an account with this exact UUID for podcast author/owner references to resolve.
- When bootstrapping a new environment (see `ops/bootstrap-production.sh`), recreate this account folder with the same UUID before relying on podcast author/owner fields.

## 5) Language & Content Conventions

- Content text should use proper German umlauts (`ä`, `ö`, `ü`, `Ä`, `Ö`, `Ü`, `ß`) where linguistically correct.
- Do not alter technical identifiers when normalizing language:
  - do not touch UUIDs, `file://...`, `user://...`, slugs, URLs.
- Panel labels should be bilingual where already established (German + English).
- Controlled vocabulary for user-facing German text:
  - A podcast episode is called **„Folge“** (plural „Folgen“), never „Episode“ in frontend copy.
  - The short notation stays international: Phase = `P`, episode within phase = `E`, overall episode number = `#` (e.g. `P3 · E61 · #188`).
  - Exception: the previous/next buttons and the current-episode label in `site/snippets/episode-pagination.php` omit the episode within the phase; the buttons separate the parts with an en space (U+2002), no glyph: `P3 TW187` (screen-reader text „Phase 3, Technikwürze 187“) and „Phase 3, Technikwürze 188“ (comma) in between.
  - When the short notation is spelled out (legend, screen-reader text), use „Phase“, „Episode“, „Technikwürze“ (e.g. „Phase 3 · Episode 61 · Technikwürze 188“); everywhere else in copy use „Folge“.
  - Brand green: `--clr-primary` for brand surfaces and large type; `--clr-primary-text` (darker in light mode, WCAG AA on yellow) for links and small text.
  - The legal provider name, address and email live only in Site → „Anbieter“ (`provider*` fields); output them via the `provider` block or the address block with source „Anbieter“, never as typed text.

- Audio player: the native `<tw-audio-player>` (`site/snippets/audio-player.php`, `src/scripts/components/audio-player.ts`, `src/styles/components/audio-player.css`) is the only player; there is no iframe and no Podlove fallback. It plays the Podcaster route `<episode>/download/<file>` (so Podcaster statistics keep counting, one hit per play, `preload="none"`), docks to the bottom of the viewport when the inline player scrolls out of view (`data-dock="started"` docks only after the first play, used in the last-episode block) and stores the play position per episode in `localStorage` (`tw-player:position:<id>`). Transcript timestamps seek the player through `src/scripts/components/transcript-sync.ts` (events `tw-player:time` / `tw-player:state`, method `seek(ms)`). Concept: `docs/concept-docked-audio-player.md`.

- Deep links: `<episode>#t=<seconds>` seeks the player, opens the transcript, marks and scrolls to the segment (`src/scripts/components/deep-link.ts`, helpers in `deep-link-time.ts`, scroll/mark via `revealPlaytime` in `transcript-sync.ts`). One share button in the player (`data-share-current`, inline and docked) shares the current position; there are deliberately no per-segment share buttons. Autoplay is attempted but usually blocked by browsers, so the visitor starts playback with one click. A cross-page persistent player is only a concept so far: `docs/concept-persistent-player.md`.

- The publication date is the third line of the native audio player (`<time datetime pubdate>`, rendered by `audio-player.ts` from `data-published*`; `.published-fallback` serves no-JS). The second line reads „Technikwürze 7 (Phase 1)“. The episode type („Reguläre Folge,“) is plain text before the download link. Episode pages also output a schema.org `PodcastEpisode` JSON-LD block with `datePublished` (and `dateModified` only when `rerelease` is set) in `site/templates/episode.php`. The right column of the player card shows only the participants.
- IndieConnector sends webmentions only on production (`config.technikwuerze.de.php`) and only on status change of an episode (publishing), never on plain updates (`send.automatically` = false). The outbox file `indieConnector.json` is git-ignored in the content repo.

## 6) Participant Model (Current State)

- Participants are pages under `content/3_teilnehmende`.
- Public visibility is controlled by native Kirby status:
  - `listed` = public
  - `unlisted/draft` = not public
- Public participant listing page uses one HTML list, CSS columns.
- The participant aside card starts with a blurred header band (pre-blurred Kirby thumb of the profile photo via `--participant-blur`, 64×40 upscaled, `blur` 16) with the round photo centered and protruding only slightly below the band's lower edge; there is deliberately no full-width hero, the page keeps the standard heading. Between 30em and 48em the panels „Statistik“ and „Profil“ sit side by side and „Im Netz“ spans both columns.
- Participant detail pages list external profiles in the aside card (panel „Im Netz“): small network icon plus the profile label, so private websites read well; networks without an icon file in `src/assets/social/` fall back to the `website` icon. The link text is the network name (e.g. „LinkedIn“) for all known networks; only `website` uses a free label (`profile_label`, shown in the Panel only for that network) and falls back to the shortened URL; there is deliberately no „other“ network, add further sites as additional `website` entries. The team roles are written as a gendered sentence in the content column („… ist bei Technikwürze Herausgeber und Moderator.“).
- Participant detail pages end with a previous/next navigation (`site/snippets/participant-pagination.php`, same `.pagination-nav` look as the episode pagination) over all listed participants sorted by last name, then first name, with „Person X von Y“ in the middle.
- Participant detail page includes computed participation stats from episode host/guest assignments.
- The „Technikwürze“ part of a participant description is a separate paragraph in prose (varied wording, no generic „war x-mal dabei“), without counts or year ranges that go stale; the numbers live in the stats panel. The last block of the content column is the role line „Rollen bei Technikwürze“ (`.participant-roles`, `.tag` per role: team roles from `additional_roles`, „Gastmoderation“ for guests), not a sentence.
- `text_review` („Text prüfen“) is set automatically on all hosts and guests when an episode gets published or its cast changes (`site/plugins/technikwuerze/lib/participant-text-review.php`, hooks `page.update:after` and `page.changeStatus:after`). It shows as „Text prüfen“ in Panel lists; clear it after checking the Technikwürze paragraph.
- `external_profiles` are re-sorted with LinkedIn first on every Panel save (`twSortExternalProfilesLinkedinFirst`, `page.update:after`); keep LinkedIn as the first entry when editing content files directly.
- Renamed participant slugs get a 301 in `site/config/base.php` (`routes`); episodes reference participants by `page://` UUID, legacy `teilnehmende/<slug>` entries in `Participants:` must be updated with the slug.
- Roles: `participant_role` is `host` („Team“) or `guest` („Gast“). Team members can have `additional_roles` (publisher/Herausgeber, moderation/Moderation, editorial/Redaktion); guests can have `guest_roles: guest_moderation` („Gastmoderation“).

## 7) Podcast/Episode Model (Current State)

- Episodes live under `content/2_mediathek/staffel-*/...`.
- Hosts/Guests are assigned via participant page references, always as `page://<uuid>` (never path ids like `teilnehmende/slug` – Kirby does not resolve them there).
- `podcasterHosts` („Team & Gastmoderation“) holds team members of the episode plus guests who host it; `podcasterGuests` holds all other guests. The episode page always labels the first group „Moderation“ (participant pages keep the roles „Team“ / „Gast“).
- Audio field in episode panel is configured to select/upload from central `site.find("audio")`.
- Episode topics: `Topics` („Hauptthema“, 1 to 3 free tags, shown on the participant stats) and `General-topics` („Allgemeine Themen“, tags restricted to the catalog the team maintains in Panel → Site → Settings → „Themenkatalog“, stored as `General-topics-catalog` in `content/site.txt`). The skill `transkript-metadaten` fills both while they are empty (`scripts/transcripts/meta.py catalog | topics | settopics`); it never extends the catalog.
- Kirby status for episodes is folder-name driven (no `Status:` field in `episode.txt`):
  - `draft`: episode folder is inside `_drafts/`
  - `unlisted`: episode folder name is `NNN-slug` (example: `001-tw188-...`)
  - `listed/public`: episode folder name is `YYYYMMDDHHMM_NNN-slug` (example: `201302031820_001-tw188-...`)
- For bulk publishing of episodes, derive `YYYYMMDDHHMM` from `Date:` in `episode.txt`.

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
- For CSS inside `@scope`, prefer collecting descendant rules under a shared `:scope { ... }` block instead of repeating `:scope .selector` for each rule.
- CSS authoring style:
  - avoid BEM for new code; prefer CSS Nesting with low nesting depth,
  - use one root component class and nest short child selectors below it (example: `.search-dialog { .header {} .input {} .actions {} }`),
  - prefer element selectors inside component scopes or logical single-class selectors,
  - avoid repeating long prefixed child class names like `.component-child-element`; bundle them under the component root instead,
  - define modifier states nested under the main component rule (example: `.tag { &.ok { ... } &.warn { ... } }`),
  - avoid repeating the same full selector for states/modifiers; nest states directly (`.link { &:hover {} &.active {} }`),
  - prefer ARIA/data/state attributes over additional utility/state classes where feasible,
  - keep responsive rules mobile-first and place `@container`/`@media` blocks at the end of a block on level 1.
- For bulk transforms, add safeguards and verify with spot checks.
- Run Prettier after edits on touched files before finishing work:
  - `pnpm exec prettier --write <files...>`
  - or `pnpm run format` for broader sweeps when appropriate.
- Special case: `site/config/vite.config.php` is auto-generated and still must be formatted with Prettier after Vite config changes (`pnpm exec prettier --write site/config/vite.config.php`), otherwise CI `format:check` can fail.
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
- On release tag push, `deploy.yml`'s `deploy` job merges the tag into `main` (`git merge <tag> --no-ff`, pushed with `secrets.RELEASE_TOKEN`) only after tests and the production build succeed, and before the rsync deploy steps run — so `main` mirrors the latest released state but a broken build never reaches `main`. This mirrors the release flow used in the `davideiken` project.

### Server model

- Webserver document root in production must point to `public/` (not repository root).
- `content/` is a dedicated Git repository on production and must exist as `content/.git`.
- Main code deployment must never overwrite `content/`, `media/`, accounts, cache or sessions.
- Main code deployment must preserve host-managed HTTP auth secrets (`.htpasswd`, including `public/.htpasswd`) via `.rsyncignore`.
- Main code deployment must preserve the activated Kirby license (`site/config/.license`) via `.rsyncignore`. It is gitignored (never present on the CI checkout), so without this exclude `rsync --delete` wipes it from production on every deploy.
- Keep `.htaccess` deploy-managed so Kirby rewrite rules in `public/.htaccess` stay consistent.
- Runtime binaries/state are not in Git:
  - `content/audio/` (audio files)
  - `content/avatars/` (participant avatar image files)
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

## 13) Overall Principles

- Do not include comments in code to show changes or explain decisions; instead, update this `AGENTS.md` and other documentation files to reflect new patterns and conventions.
- Always prefer explicit, readable code and configuration over implicit or "magic" behavior.
- If writing comments to point out non-obvious behavior, consider if the code can be refactored to be more self-explanatory instead.
- If writings comments, use english language.
