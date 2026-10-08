# Concept: Persistent player across page navigation

Status: proposal, nothing implemented. Date: 2026-10-07.
Related: `docs/concept-docked-audio-player.md` (native player, dock), deep links (`#t=`, implemented, see §9).

## 1. Goal

Playback of an episode continues without interruption while the visitor navigates (episode page → home → Mediathek …). The docked bar stays visible on every page, although the markup of the episode page is gone.

## 2. Findings: what the platform can and cannot do

A normal (multi-page) navigation destroys the document and with it the `<audio>` element. There is no API to hand a playing media element to the next document.

| Idea                               | Verdict                                                                                                 |
| ---------------------------------- | ------------------------------------------------------------------------------------------------------- |
| Media Session API                  | Lock-screen controls only; dies with the document.                                                      |
| Cross-document view transitions    | Animate between pages (already used via `@view-transition`), do not preserve elements.                  |
| Service Worker / SharedWorker      | Cannot play audio. BroadcastChannel only syncs state.                                                   |
| bfcache                            | Only helps back/forward, pauses media.                                                                  |
| Document Picture-in-Picture        | Chromium only, needs a user gesture, floating window instead of a bar. Possible add-on, not a solution. |
| iframe shell around the whole site | Breaks URLs, a11y, SEO, scroll behaviour. Rejected.                                                     |
| **Soft navigation (fetch + swap)** | **The only real option.** The player lives in a container that is never replaced.                       |

## 3. Options for the soft-navigation layer

| Option | Description                                                                                                                                                       | Verdict                                                                                                                |
| ------ | ----------------------------------------------------------------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------- |
| A      | Turbo Drive with `data-turbo-permanent` on the dock                                                                                                               | Purpose-built, but ~30 KB and a new runtime dependency (project has almost none).                                      |
| B      | **Own thin layer (~150 lines)**: Navigation API (`navigate` + `intercept`) or click + `popstate` fallback, `DOMParser`, body swap, `document.startViewTransition` | **Recommended.** Matches "explicit over magic". Unsupported browsers keep the normal MPA behaviour (audio just stops). |
| C      | htmx / Swup                                                                                                                                                       | Like A, no advantage.                                                                                                  |

Navigation API support: believed to be baseline since early 2026; verify on caniuse before building. The click/popstate fallback makes this non-critical.

Open decision (2026-10-07): own layer (B) vs. Turbo (A). Default: B.

## 4. Architecture

1. **Player engine as a singleton module.** Owns the only `Audio` object, state, Media Session and position storage. `<tw-audio-player>` becomes a pure view without its own `<audio>`; inline view and dock are views of the same engine. This is the largest refactor: `audio-player.ts` currently holds everything in one class. Do it as a separate first step, without navigation, behind unchanged behaviour.
2. **Persistent dock.** A `<tw-player-dock>` at the end of `layout.php` outside the swap region. Navigation replaces all body children except this container. `html` attributes (theme) are not swapped.
3. **Dock rule.** Visible when an episode is loaded and its inline view is not visible. On home/Mediathek always; on the episode page of the playing episode the inline view takes over. The dock needs a close button (the old concept deliberately had none).
4. **Switching episodes.** Playing another episode on page B switches the engine source. Stats: one `download/` hit per play, the continuing episode causes no new hit.
5. **Per-page re-init.** `header-nav`, `search-dialog`, `komments`, `transcript-words`, `brand-networks`, `view-transitions` hang on `DOMContentLoaded`. Make them idempotent and trigger them from a `tw:page-load` event; register document-level listeners only once. `transcript-sync` and `deep-link` already mostly work this way (delegated listeners).
6. **View transitions.** Replace the CSS `@view-transition { navigation: auto }` by `startViewTransition` in the router, otherwise pages animate twice.
7. **Do not intercept:** POST forms (contact, comments), downloads, `target=_blank`, external links, Panel, feed, modifier-key clicks, links with an opt-out attribute (e.g. `data-no-soft-nav`).
8. **Deep links.** The `#t=` handler (`initDeepLink`) must run on `tw:page-load` as well, so `…/folge#t=754` works via soft navigation.

## 5. Optional: resume after a hard reload

The engine is empty after a reload. Remember the last played episode (id, title, poster, audio URL, duration) in `localStorage` and show the dock paused with "Weiterhören". With `preload="none"` this costs no stats hit until the user presses play.

## 6. Risks

- `<head>` differences between pages: title, meta, canonical, page-specific scripts (dynamic `brand-networks` import).
- Accessibility after the swap: update `document.title`, reset focus (to `main` or the heading), announce the page change in a live region.
- Scroll handling for back/forward (restore position) and for anchors.
- Third-party scripts or analytics that assume full page loads: check before building.
- Cache: soft-navigated pages are fetched like normal pages, no Kirby change needed.
- Effort: roughly 3-5 days, mostly engine refactor and re-init.

## 7. Work plan

1. Extract the engine from `tw-audio-player` (no behaviour change, tests for engine state, skip clamping, storage).
2. Make page scripts idempotent behind a `tw:page-load` event.
3. Add the router (swap, history, scroll, focus, view transition, opt-outs); feature-detect and fall back to MPA.
4. Persistent dock + dock rule + close button.
5. Optional resume after reload.
6. a11y (VoiceOver, keyboard, live region), cross-browser (Safari iOS, Firefox, Chrome), stats check (one hit per play across navigation).
7. Update `CLAUDE.md` (player section) and `docs/concept-docked-audio-player.md` §9 (non-goal no longer applies).

## 8. Alternatives worth remembering

If soft navigation turns out too risky: ship only the Document Picture-in-Picture button (Chromium) as a progressive enhancement, or keep the current behaviour (position is stored per episode, resume works on return).

## 9. Deep links (implemented 2026-10-07)

Not part of this concept, listed because it interacts with it.

- Format `…/<episode>#t=<seconds>` (fragment: no server request, no cache variants). Pure helpers in `src/scripts/components/deep-link-time.ts` (unit-tested), behaviour in `deep-link.ts`.
- On load and `hashchange`: seek the player (overrides the `localStorage` resume position), open `details.tw-transcript`, mark and scroll to the segment (`revealPlaytime` in `transcript-sync.ts`), try `play()`.
- Autoplay with sound is blocked by browsers without a prior user gesture (always in Safari); the visitor lands on the marked position with the dock showing "play" and starts with one click.
- Share button: only in the player (`data-share-current`, shares the current position), inline and in the dock. Per-segment buttons in the transcript were tried and dropped (no hover on touch, a button inside the plugin's timestamp `<button>` is invalid HTML, 360 extra buttons). If sharing without playing is wanted later: one single floating button that follows the hovered/focused segment. Touch devices use `navigator.share`, desktop copies to the clipboard.
