---
name: transkript-import
description: Transcribe a newly produced Technikwürze episode with speaker labels and import it as a tw-transcript block into the episode's Kirby page using the versioned scripts in scripts/transcripts/ (ElevenLabs by default, free local Whisper as fallback), including quality gate and archive. Use for every "transkribiere Folge X" task.
---

# Technikwürze transcript import

Transcribes one podcast episode and inserts the result as the first block on its Kirby episode page. Everything is done
with the versioned scripts in `scripts/transcripts/` (see its `README.md`, which explains every script) — **never
re-create helper scripts in the scratchpad or in `migration/`** (that folder is a legacy migration workspace, not used by
this workflow); extend the scripts there instead. API keys live in the git-ignored `.env`, work data (raw/converted
transcripts, venv, logs, draft backups) in the git-ignored `.work/` (paths: `scripts/transcripts/lib/paths.py`).

## When to use

A newly produced episode: the audio is in `content/audio/`, the episode page exists in `content/2_mediathek/` (title,
shownotes, hosts/guests as David entered them). Run the pipeline below for that one episode with the best quality
(ElevenLabs, proofreading, `verify.py` gate).

Editorial judgment calls (speaker naming, Einspieler, toggle choice, metadata deviations) are described below; metadata
fixes are done by the skill `transkript-metadaten`. **After the transcript is imported, always run
`transkript-metadaten` for that same episode before moving on.**

## Quality bar

The transcript must contain nothing false: no non-existent speakers (also no combined labels like "Dirk/Jens"), no wrong
speaker attribution, no wrong or nonsensical words, no English passages garbled into German. Light smoothing of
sentences is fine. When in doubt leave the text as transcribed and tell David instead of guessing. The gate is
`verify.py` (step 6) plus a final read of the first and last two minutes and one random passage.

## Choose the engine

| | ElevenLabs (`transcribe-episode.py`) | Local (`transcribe-episode-local.py`) |
|---|---|---|
| Quality | best: names, technical terms, completeness, speakers | usable, but more word/name errors, drops phrases, shared microphones split speakers badly |
| Cost | pay-as-you-go credits, ~10 per audio minute (≈ 4 cent/min, a 60-minute episode ≈ 25 cent) | free, ~0.22× real time, one process at a time (16 GB RAM) |
| Setup | `ELEVENLABS_API_KEY` in `.env` | `scripts/transcripts/setup-local.sh` once, `HF_TOKEN` in `.env` |

**Default to ElevenLabs.** If credits run out (`quota_exceeded`), stop and tell David — never buy credits yourself. A
local transcript needs full proofreading (step 5) and should be replaced by ElevenLabs when credits are available.

## Per-episode pipeline

All commands run from the repository root. `N` = episode number.

### 1. Transcribe

```
python3 scripts/transcripts/transcribe-episode.py N content/audio/twN.mp3 --language deu     # ElevenLabs
scripts/transcripts/setup-local.sh                                                            # once: .work/venv + spell checker
.work/venv/bin/python scripts/transcripts/transcribe-episode-local.py N content/audio/twN.mp3   # local
scripts/transcripts/prep.sh N                                                                 # local only: copy to .work/transcripts/segments/
```

ElevenLabs writes `.work/transcripts/raw/twN.json` and `.work/transcripts/segments/twN.json`. The local script writes to
`.work/transcripts/local/`, detects non-German stretches (e.g. English interviews) and re-transcribes them in their
language. Check the shownotes first: an episode can be very short on purpose, or taken offline (no audio, no transcript).

### 2. Identify the speakers

```
python3 scripts/transcripts/sm.py N      # ElevenLabs / segments/twN.json
python3 scripts/transcripts/lsm.py N     # local transcript before prep.sh
```

Shows per raw speaker id: segments, characters and sample lines. Read the first lines (self-introductions, "Hallo
Marcel", who greets whom) and the outro; look at the long segments of every speaker. Rules:

- Use first names as labels (`David`, `Marcel`, `Dirk`); disambiguate with a surname only when two people share the
  first name (`Dirk Ginader`, `Dirk Jesse`). `Marcel Otten` = `Marcel Böttcher` (alias, confirmed).
- Intro/outro jingles, section titles ("Aktuelles."), songs, pre-produced clips → `Einspieler`.
- Listener comments → the first name if announced ("Basti"), else `Hörer`. Audience at live recordings → `Publikum`.
  Interviewees without a name in the audio → `Interviewpartner`. Never guess a name that is not audible or in the
  shownotes; `Sprecher 1/2` is acceptable.
- Diarization drifts: one person split over several ids, two people merged, jingle merged into a speaker. Merge ids
  that are clearly the same person, and fix single segments by time (step 3).

### 3. Map the speakers

```
python3 scripts/transcripts/map.py N '{"speaker_0":"David","speaker_1":"Einspieler",...}' ['{"00:18,3":"Einspieler"}'] [--rebuild]
```

Maps every raw id (it exits with an error if one is left unmapped), merges consecutive turns of the same speaker and
applies optional per-segment fixes keyed by the start time prefix. **Not idempotent:** a second run does not remap
already mapped names; `--rebuild` regenerates the unmapped turns from the raw file first. A fix key may report `MISSING`
when the segment was merged — then correct the label afterwards with `setspeaker.py`.

### 4. Import

```
python3 scripts/transcripts/imp.py N auto [--replace]
```

Prepends the `tw-transcript` block to the episode's `Blocks` field in `episode.txt` (direct file edit; handles both
`Blocks: [...]` and `Blocks:` with the value on the next line). `auto` sets "Anzeige beim Laden" to open (`true`) for
short shownotes (< 1000 characters) and closed (`false`) otherwise; `--replace` swaps an existing transcript block. It refuses to duplicate a block and prints `CHANGES`
if the episode has a Panel draft (`_changes/episode.txt`) — such a draft would overwrite the transcript when published;
report such a draft to David instead of deleting it.

Block shape: `{"type":"tw-transcript","id":"<uuid>","isHidden":false,"content":{"headline":"Transkript","intro":"",
"initialstate":"true"|"false","hiderepeatedspeakersuntilchange":"false","segments":[{"speaker","timestamp","text"}]}}`
with timestamps like `"1:28:18"` (no milliseconds, no leading hour zero).

To replace an existing block (e.g. a local transcript by an ElevenLabs one) run the pipeline again and import with `--replace`.

### 5. Fix names and obvious errors

```
python3 scripts/transcripts/globalfix.py N                    # known name/term errors (Technikwürze, Webkrauts, Maciejewski, ...)
python3 scripts/transcripts/textfix.py N '{"old":"new"}'      # episode specific, exact substrings
python3 scripts/transcripts/proofread.py N                    # suspicious words (spell checker + vocabulary of all transcripts)
python3 scripts/transcripts/setspeaker.py N 12:36 Einspieler  # relabel one segment of the published block
python3 scripts/transcripts/rename_speaker.py N old new       # rename a label in the published block
```

These work on the imported block, so run them after step 4. Only correct what is **certain** (names from the shownotes,
obvious ASR typos, URLs); unclear passages stay as transcribed. Add new recurring corrections to the list in
`globalfix.py` (version-controlled). A *local* transcript needs the whole `proofread.py` review.

### 6. Verify (quality gate)

```
python3 scripts/transcripts/verify.py N
```

Checks the imported block against the episode page: raw `speaker_N` labels, labels that are neither a participant nor a
standard label (listener/audience/guest missing in the metadata?), participants without any segment, empty or duplicated
segments, backwards timestamps, passages that look English, jingles labelled as a person, known name misspellings.
**Errors must be fixed, every warning must be explained or fixed** (listeners/audience labelled by first name and English
interviews are expected warnings). Then read the first and last two minutes and one random passage of the block.

### 7. Metadata

Run the skill `transkript-metadaten` for the same episode (adds missing hosts/guests, creates participant stubs, sets the main and general topics from the team's topic catalog while they are empty). Then
re-run `verify.py N`.

### 8. Archive the word-level data

```
python3 scripts/transcripts/archive-transcripts.py N
```

Writes `content/.transcripts/twN.json.gz` (words with timestamps, raw speaker ids, id→label mapping; Kirby ignores the
dot folder). It lives in the content repo so the data can be reused later (subtitles, synced player, search). Commit
only when David asks.

## Typical pitfalls (learned from transcribing the whole back catalogue)

- **Metadata rarely matches the audio.** Older pages often list only David or only the team, while the audio has
  co-hosts, a news reader (the news rubric was usually read by Nadja), interview guests, listeners. The intro ("heute mit
  …") names the hosts and guests; David may be absent and Marcel host alone. Hand findings to `transkript-metadaten`.
- **Remote recordings (Skype etc.):** a participant who drops out and returns gets a *new* speaker id — merge it using
  dialog cues ("jetzt ziehen wir noch mal den Dirk rein"). Two voices can be merged into one id after a dropout, and two
  people at one microphone in the same room always share one id. **Never invent a combined label** such as `Dirk/Jens`.
  Use addressing ("Marcel?" → answer), self-introductions and the outro to assign every turn to one person; when it stays
  ambiguous, say so to David instead of guessing.
- **ElevenLabs can change a speaker's id in the middle of a conversation** (an interviewer keeps one id for a while, then
  gets another) and can merge two voices into one id. Check long interviews turn by turn; fix by time (`map.py` fixes or
  `setspeaker.py`). **Forcing `--speakers N` can make it worse** (merges everything) — run without it first.
- **Rubric titles and songs attach to a neighbouring speaker id.** "Aktuelles.", "Das Thema.", "Interview.", "Empfehlungen.",
  the intro/outro jingle and songs (including lyrics, often English) must be `Einspieler`; check the first and last
  segments and run `jinglefix.py`. When the same id also carries a host, mapping merges the jingle into the host's turn —
  split such turns by hand. Screen-reader samples read out in an episode about accessibility are `Einspieler` too.
- **Pre-produced or foreign contributions inside an episode:** listener voice mails (hotline), clips from other podcasts,
  a contributed audio comment, an article read by a listener ("Diesen Artikel liest jetzt Arne"), flashbacks. The reader of
  an announced contribution is a participant (metadata), the other cases are `Einspieler`/`Hörer`.
- **Live recordings** (Webmontag, barcamps, conferences): moderator plus many unnamed voices → `Publikum` or
  `Interviewpartner`; name only people the host announces. Interviewers of partner media (e.g. T3N) are real participants.
- **English guests or whole English episodes** (interviews with international guests): ElevenLabs detects the language itself; local Whisper must not be forced to German (the script detects and re-transcribes
  stretches). `verify.py` warns about English passages — expected there, a bug elsewhere.
- **ASR misspells names and the brand** ("Technikwitze", "Technikwirtze", "Webcrowds", "Maczewski" …) and sometimes mishears
  a first name ("Arne" as "Hannes"). Take spellings from shownotes, the participant list and `globalfix.py`; add recurring
  errors there. People are often introduced by nicknames or first names only ("Schepp" = Christian Schäfer): ask David
  when unsure, and do not "correct" a word you cannot verify.
- **`Marcel Otten` = `Marcel Böttcher`** (same person, spelled both ways in old descriptions).
- **Short or missing audio can be legitimate:** a 3-minute announcement is a real episode; an episode taken offline has no
  audio and no transcript. Check the shownotes before treating it as an error.
- **Local Whisper in practice:** it drops phrases and invents unusual words for names and technical terms, and a 16 GB Mac
  swaps when two Whisper processes run. ElevenLabs was clearly better in every comparison.

## Stop rules

Stop and ask David on: import errors, unclear speaker assignment you cannot resolve from the audio, missing audio,
quota/cost messages. Do not publish, rename or change other fields of an episode. Do not delete `_changes/` drafts
without being asked (back them up first when told to discard them). Do not commit unless asked.

## Optional: kirby MCP

If the `kirby` MCP server is connected, `kirby_read_page_content` / `kirby_update_page_content` can read and write the
`blocks` field instead of `imp.py`; the scripts above are the tested default and need no MCP.
