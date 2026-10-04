---
name: transkript-import
description: Transcribe a Technikwürze episode and import it as a tw-transcript block into the episode's Kirby page, using the kirby-mcp server (or direct content-file edits as fallback) instead of browser automation. Use for any "Folge X transkribieren" task from docs/transkripte/arbeitsliste.md.
---

# Technikwürze transcript import

Transcribes one podcast episode and inserts the result as the first block on
its Kirby episode page. This replaces the earlier Chrome/Panel-automation
workflow (screenshots, importer UI, manual Speichern) — it was token- and
time-expensive for no benefit, since Kirby content is plain text files and
(now) reachable through a real Kirby runtime via MCP. Expect roughly
80–90% fewer tokens and no flaky UI retries per episode.

Work the list in `docs/transkripte/arbeitsliste.md` top to bottom, one
episode fully at a time. Read that file fresh at the start of a session —
**Hinweis 1–3** at the top define the role model (Team/Gast/Gastmoderation),
numbering history, and audio-replacement notes; they still apply unchanged.
Everything below is only about *how* to move data into Kirby, not the
editorial judgment calls (speaker naming, Einspieler, toggle choice,
`⚠ Metadaten:` notes) — those are unchanged from before and described in
Hinweis 3.

## Prerequisites

- Local dev server running at `http://127.0.0.1:8000`.
- The `kirby` MCP server is registered in `.mcp.json` (project-scoped,
  `vendor/bin/kirby-mcp`). On a fresh session it shows as "Pending approval"
  in `claude mcp list` until the user approves it once — ask them to run
  `claude` interactively (or otherwise approve) if `kirby_*` tools are not
  in your tool list. If it's genuinely unavailable, use **Fallback B**
  below instead of blocking.
- `migration/scripts/transcribe-episode.py` and
  `migration/scripts/apply-speaker-mapping.py` are unchanged and still do
  the transcription + speaker-mapping work locally (no tokens spent on
  this part either way — it's a local script call).

## Per-episode pipeline

### 1. Transcribe

```
python3 migration/scripts/transcribe-episode.py <N> content/audio/tw<N>.mp3 --language deu
```

Inspect the raw per-speaker segments (`migration/data/transcripts/tw<N>.json`)
the same way as before: read enough of each `speaker_N`'s lines to name
real participants, spot Einspieler (jingles, section titles, ads, listener
voicemails, pre-recorded clips/flashbacks), and catch diarization drift
(the same person split across multiple speaker IDs — merge them under one
name, as documented in several worklist Notiz entries).

### 2. Map speakers

```
python3 migration/scripts/apply-speaker-mapping.py <N> '{"speaker_0":"David", ...}'
```

Produces `migration/data/transcripts/tw<N>.json` with `segments: [{speaker,
start_time, text}]`, `start_time` as `"MM:SS,mmm"` / `"H:MM:SS,mmm"`.

### 3. Find the target page and read its current state

Resolve the Kirby page id from the episode's folder: it's
`mediathek/p<phase>/tw<N>-<slug>` (the same id you'd see in the Panel URL
with `+` replaced by `/`). If unsure, grep the content folder:

```
find content/2_mediathek -maxdepth 2 -iname "*_tw<N>-*"
```

With the MCP connected:

```
kirby_read_page_content(id: "mediathek/p<phase>/tw<N>-<slug>")
```

This returns the page's content fields, including `blocks` (JSON string)
and the Date field. From the decoded `blocks` array:
- Check whether a `tw-transcript` block already exists — if so, stop and
  flag it (don't duplicate).
- Find the `markdown` block(s) holding the shownotes and read their
  `content.text` — this is what you judge minimal vs. substantial on for
  the "Anzeige beim Laden" toggle (same rule as before: open/Aufgeklappt
  for minimal shownotes, closed/Zugeklappt default for substantial ones).

### 4. Build the transcript block and write it

Convert each segment's `start_time` by dropping the millisecond part after
the comma (`"1:28:18,340"` → `"1:28:18"`, `"00:09,980"` → `"00:09"`). This
matches exactly what the old Panel importer produced — confirmed against
already-imported episodes (e.g. `tw129`'s last segment timestamp is
`"1:28:18"`, no leading zero on the hour, no milliseconds).

Build one block object:

```json
{
  "type": "tw-transcript",
  "id": "<uuid>",
  "isHidden": false,
  "content": {
    "headline": "Transkript",
    "intro": "",
    "initialstate": "true" | "false",
    "hiderepeatedspeakersuntilchange": "false",
    "segments": [{"speaker": "...", "timestamp": "...", "text": "..."}, ...]
  }
}
```

(`initialstate`: `"true"` = Aufgeklappt/open, `"false"` = Zugeklappt/closed
— per your toggle decision from step 3.) Get a fresh id from
`kirby://uuid/new` (or any UUID v4 generator — Kirby doesn't care about the
source as long as it's a valid UUID and unique among the page's blocks).

Prepend it to the existing `blocks` array (transcript block always first,
per repo convention) and write back with:

```
kirby_update_page_content(
  id: "mediathek/p<phase>/tw<N>-<slug>",
  data: { "blocks": <json-encoded full array> },
  confirm: true
)
```

This calls Kirby's own `$page->update()` — it writes straight to the
published ("latest") content, validated against the page's blueprint, with
no separate "Speichern" step and no browser involved. Re-run
`kirby_read_page_content` afterward (cheap, no images) to confirm the
block landed and the segment count / speaker set look right.

### 5. Update the worklist

Same as before: in `docs/transkripte/arbeitsliste.md`, flip `[ ]` to `[x]`
for the row, and add a `⚠ Metadaten:` Notiz per Hinweis 3 if the audio
revealed participants, roles, or involvement the page metadata doesn't
reflect. Never regenerate the table, only edit the one row's cells.

## Fallback B: direct content-file edit (no MCP)

If the `kirby` MCP tools aren't available in this session, you can still
avoid the browser by editing the content file directly — it's plain text,
confirmed against multiple already-imported episodes:

- File: `content/2_mediathek/<phase-dir>/<datefolder>_tw<N>-<slug>/episode.txt`
- Fields are `Key: Value` blocks separated by a line containing only `----`.
- The `Blocks:` field's value is a **single-line, compact JSON array**
  (`json.dumps(..., ensure_ascii=False, separators=(",", ":"))` in Python —
  no spaces after `:`/`,`, umlauts written literally, not `\uXXXX`-escaped).
- Read the file, locate the `Blocks: [...]\n\n----` span, parse the JSON,
  prepend the new block (same shape as step 4 above), re-serialize with
  the exact same compact style, and write only that span back — leave
  every other field byte-identical. Use Read + Edit, not Write, so the diff
  stays minimal and auditable.
- **Before editing**, check
  `content/.../<episode-dir>/_changes/episode.txt` — if it exists, that
  episode has an unpublished Panel draft (there were 58 such stray drafts
  found in this repo on 2026-10-04, apparently left over from an earlier
  bulk "Sprecher aus Metadaten ergänzt" edit, unrelated to transcripts).
  Editing `episode.txt` directly does not touch `_changes/`, so the Panel
  will still show that old pending-changes banner next time someone opens
  the page — this is pre-existing, not something this workflow causes.
  Don't delete or merge `_changes/` content without being asked.
- No cache flush is needed — the project has no page-content cache
  configured; Kirby reads the content file live.

## What not to do anymore

- No `tabs_context_mcp` / `navigate` / `computer` screenshots for this task.
- No typing into the importer's "Zielseite suchen" field, no clicking
  through search-result flakiness.
- No "Verwerfen"/"Speichern" clicking, no pencil-icon block-editor drawer
  for the toggle — set `initialstate` directly in the JSON you write.
- Browser automation is still fine for *other* tasks in this project
  (visually checking a rendered page, debugging CSS, etc.) — just not for
  moving transcript data into Kirby.
