# Concept: Docked audio player (replacing the sticky Podlove card)

Status: proposal, nothing implemented. Date: 2026-10-06.

## 1. Problem

On `site/templates/episode.php` the Podlove player lives in a card (`.episode-player-sticky`, `src/styles/components/podcast-player.css`, sticky from 48em) together with ordinal, download link, hosts and guests. With many participants (e.g. tw156) the sticky card gets taller than the viewport and covers the content.

Podlove is also expensive to keep styled: it runs in an iframe, so `site/plugins/kirby-tw-transcript/src/podlove-player.ts` (~1100 lines) patches theme tokens and CSS into the iframe, remounts on theme change and guards against seek glitches.

## 2. Goals and constraints

- Player stays at the top of the episode page; a lightweight variant stays fixed at the bottom while scrolling (Spotify style, full width).
- Participants, ordinal and download move out of the player into normal page flow.
- Stats keep counting through the Podcaster download route.
- Scrubbing, ±skip and speed work locally (dev) and on production.
- Accessible (WCAG 2.2 AA) and UX best practice.
- Transcript timestamps (plugin `kirby-tw-transcript`) keep seeking and following playback.

## 3. Options

| Option | Description                                                                               | Verdict                                                                                  |
| ------ | ----------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------- |
| A      | Keep Podlove, add a second compact Podlove template as the bottom bar                     | Two instances = two `<audio>` elements, double state, double iframe theming. Rejected.   |
| B      | Use Podlove store as a headless engine, build own UI                                      | Audio still lives in the Podlove iframe, store API is internal, little gain. Rejected.   |
| C      | **Own player on a native `<audio>` element and `<button>`/`<input type=range>` controls** | Full design freedom, no iframe, theming through CSS variables, less JS. **Recommended.** |

Feasibility of C: yes. `<audio>` supports everything required (play/pause, `currentTime`, `playbackRate`, `volume`, `seekable`, Media Session API). Podlove features we would lose and must consciously decide on: share/embed UI, subscribe menu, in-player chapter list, Podlove transcript tab (we have our own transcript block). Chapters are rebuilt as a small list/marker set from `Feed::getChapters()`.

## 4. Architecture

### 4.1 One audio element, two presentations

One `<tw-audio-player>` custom element (TypeScript, `src/scripts/components/audio-player.ts`) owns exactly one `<audio>`. It has two views driven by the same state:

- **Inline view** (top of the episode page, inside the page header area): cover, title/ordinal, large play button, ±skip, seek slider, times, speed, volume, chapters, download link.
- **Dock view** (`position: fixed; inset-inline: 0; inset-block-end: 0`): cover thumbnail, title, play, ±skip, current time, seek slider, speed, current chapter. No participants, no download.

The dock appears when the inline view leaves the viewport (`IntersectionObserver`) and hides again when it returns. Never two audio elements: the dock is the same element restyled (container mode via a `data-docked` attribute), or the inline view and the dock are two light views bound to the shared element's state. Prefer the first: a single DOM node, switched with `position: fixed`, avoids state sync.

Alternative if a persistent bar is preferred: show the dock as soon as playback first starts. Recommendation: dock on scroll-out, and keep it while playing even if the user scrolls back up only if the inline view is hidden. Simplest rule: `docked = !inlineVisible`.

### 4.2 Markup (progressive enhancement)

Server-rendered snippet `site/snippets/audio-player.php` replaces `podcast-media`/`podcast-player` for episodes:

```html
<tw-audio-player data-duration="3912" data-chapters="[...]" data-title="…" data-poster="…">
  <audio controls preload="none" src="/mediathek/p3/tw156-…/download/tw156.mp3"></audio>
</tw-audio-player>
```

Without JS the native `controls` remain usable. JS removes `controls`, builds the UI and hides the fallback. Duration and chapters come from the server (`getAudioDuration`, `getChapters`), so no metadata request is needed to render the UI.

### 4.3 Other changes in the episode template

- Remove `.episode-player-sticky` and `is-sticky-enabled` (CSS and the sticky logic at `podlove-player.ts:611-641`).
- Move hosts/guests into their own `<section aria-labelledby>` below the player, normal flow, responsive grid. This is what fixes tw156.
- Ordinal and download link stay with the inline view.
- Add `padding-block-end` equal to the dock height to the page and `scroll-padding-block-end` on `html`, so the dock never covers the footer or focused elements (WCAG 2.4.11).

## 5. Statistics (must keep working)

Decision: counting stays exactly as it is today; no patch to Podcaster.

Verified facts (2026-10-06):

- The audio URL is `<episode-url>/download/<file>` (`Feed::getAudioEnclosures`). Route `plugin/routes.php` (`GET|HEAD`) calls `trackEpisode()` for every non-HEAD request; bots are filtered by user agent.
- The route returns a `File`, which Kirby answers with a `307` redirect to the static `/media/pages/audio/<hash>/<file>.mp3`. Production: `HEAD` follows the redirect to `200`, `Accept-Ranges: bytes`, `Content-Length: 29972186`, served by Apache. Range requests (seeks) therefore go to the static file and are not counted; only the hit on `/download/` is.
- Plan: the `<audio src>` uses the same `download/` URL, so one play = one counted request, like with Podlove. `preload="none"` so that a page view alone never hits the route (duration comes from the server).
- To verify with the new player: one play plus several seeks must add exactly one row (Chrome, Firefox, Safari). Check on staging, because local dev behaves differently (see §6).

## 6. Scrubbing and seeking

Seeking in streamed MP3 requires `206`, `Accept-Ranges: bytes`, `Content-Range` and a correct `Content-Length`.

- Production: works natively, Apache serves the static media file after the redirect (verified with `HEAD`; a Range `GET` was not sent to avoid adding a counted download).
- Local dev: the PHP built-in server does not handle Range on static files. The `route:after` hook in `site/plugins/technikwuerze/extensions/hooks.php` (debug only) answers `/download/` with `Response::file()` instead of the redirect, which handles Range in Kirby 5. Verified locally: `206`, `Content-Range: bytes 0-1/29972186`. Side effect: in dev every Range request hits the route and is counted locally; irrelevant for production numbers.
- No production change is needed; the debug-only hook stays as the dev workaround. The player needs no special handling.
- Player side: set `audio.currentTime` only after `seekable` covers the target; keep the slider value optimistic while seeking (`seeking`/`seeked`); handle `waiting`/`stalled` with a busy indicator.
- Skip buttons: -15 s / +30 s, clamped to `[0, duration]`.

## 7. Transcript and chapter integration

`kirby-tw-transcript` currently dispatches `PLAYER_REQUEST_PLAYTIME` / `PLAYER_REQUEST_PLAY` to the Podlove store and subscribes for time updates. Introduce a small player interface instead of the Podlove store:

- `tw-audio-player` exposes `seek(ms)`, `play()` and emits `timeupdate`-based CustomEvents (`tw-player:time`, `tw-player:state`) with `detail.playtime` in ms.
- The plugin's `podlove-player.ts` is reduced to a thin adapter (transcript follow, timestamp buttons). The Podlove-specific glitch guards (`MANUAL_SEEK_GLITCH_GUARD_MS`, end-of-track guards, iframe theming, remount) can be removed once the native element is in place.
- The plugin lives in its own repository (VCS plugin), so ship the adapter as a new minor version and keep the Podlove adapter selectable for a transition period.
- Keep `site/snippets/podcaster-podlove-player.php` and the Podlove assets until the old path is removed; select per feed through `playerType` (`podlove` | new `twz`), so rollback is a config switch.

## 8. Accessibility

- Landmark: `role="region"` with `aria-label="Audioplayer: <episode title>"` (the dock keeps the same label; no duplicate landmarks because it is the same element).
- Controls are native `<button>`s with text alternatives. Play/Pause changes its accessible name ("Wiedergabe" / "Pause"); do not also use `aria-pressed`.
- Seek: native `<input type="range">` with `aria-valuetext` such as "12 Minuten 30 Sekunden von 1 Stunde 5 Minuten". Update `aria-valuetext` on input/change and at most every few seconds during playback to avoid screen reader chatter. Arrow keys ±5 s, PageUp/PageDown ±30 s, Home/End.
- Status announcements (loading, error, chapter change) in one polite `aria-live` region; no announcement per `timeupdate`.
- Keyboard shortcuts only while focus is inside the player (WCAG 2.1.4): Space/K play, J/L skip, M mute. No global shortcuts.
- Target size at least 44×44 px for primary controls, never below 24×24 px (WCAG 2.5.8). Visible focus ring, contrast AA in light and dark (`--clr-primary-text` for small text), `forced-colors` support.
- `prefers-reduced-motion`: no slide-in animation for the dock, only an instant show/hide.
- Fixed dock must not obscure focus: `scroll-padding-block-end`, page bottom padding, `env(safe-area-inset-bottom)` on iOS.
- Reflow: at 320 px / 400 % zoom the dock uses two rows (controls above, slider below) and drops volume and chapter text; volume is hidden on touch devices (system volume).
- Errors: if the audio fails, show a visible message plus the download link.
- Media Session API: title, artist (show), artwork, play/pause/seek/skip handlers for lock screen and headphones.

## 9. UX details

- Resume: store position per episode in `localStorage` (try/catch), offer "Weiter bei 12:30" instead of auto-seeking.
- Persistence across page navigation is not possible in an MPA without extra work; document as non-goal for now (the dock stops on navigation). A later option is a persistent shell via cross-document view transitions or a small SPA-style navigation layer.
- Dock hides on the print stylesheet. A close button is not needed because the dock only shows while the inline view is out of view.
- Speed options 0.75 / 1 / 1.25 / 1.5 / 2 in a native `<select>` or menu button (select is most accessible).

## 10. Styling

- New `src/styles/components/audio-player.css` following the repository CSS rules: CSS nesting with a single root class, logical properties (`inline-size`, `inset-block-end`), mobile first, `@container`/`@media` at the end.
- Theming through existing tokens (`--clr-primary`, `--clr-primary-text`, surface variables); light/dark via the existing theme switch, no iframe patching.
- Bilingual labels are only needed in Panel blueprints; UI strings stay German ("Folge", never "Episode" in copy).

## 11. Work plan

1. Done: production Range and stats behaviour verified (see §5, §6).
2. Build `audio-player.php` snippet and `tw-audio-player` element (inline view only), behind `playerType`.
3. Add the dock mode (IntersectionObserver, CSS, padding/scroll-padding).
4. Restructure `episode.php`: participants section, remove sticky card and sticky JS.
5. Transcript plugin adapter (events), release as plugin minor version.
6. Chapters, Media Session, resume position.
7. Tests: Vitest for time formatting/skip clamping; manual or Playwright for seek, dock, stats; a11y checks (axe via the a11y MCP, Lighthouse, VoiceOver, keyboard-only, 320 px, 200 % zoom); Safari iOS, Firefox, Chrome.
8. Remove Podlove assets, iframe theming code and `PLAYER_*` glue after one release cycle without regressions; update `CLAUDE.md`/`AGENTS.md` (Podlove section).

## 12. Risks

- Browsers might re-request the `download/` URL instead of the redirect target on seek, which would inflate counts. Verify on staging (see §5).
- Losing Podlove's share/embed feature is accepted.
- Transcript plugin is a separate repo; coordinate versions.

## 13. Decisions (2026-10-06)

1. Counting unchanged, full support of the existing Podcaster statistics.
2. Dock appears only after the inline player scrolls out of view; scrolling back up shows the inline player at its original place and the dock fades out (`docked = !inlineVisible`).
3. Podlove share/embed is not needed.
4. Skip values -15 s / +30 s.
5. Goal: no iframe at all.
6. Native player is the default; the Podlove fallback, `public/assets/podlove/` and the Podlove snippets were removed. Transcript sync moved to `src/scripts/components/transcript-sync.ts`; the plugin's `initPodlovePlayers` is no longer used.
