# Transcript toolkit

Scripts for turning an episode's audio into a speaker-labelled transcript and importing it into Kirby. They are driven by
two agent skills that describe the workflow and the editorial rules:

- `.claude/skills/transkript-import` — transcribe, label speakers, import, quality gate, archive
- `.claude/skills/transkript-metadaten` — make the episode's hosts/guests match the audio

Run everything **from the repository root** (`scripts/transcripts/<script>`). This README explains what each script does so
the pipeline can be understood, repaired or extended without the skills.

## Overview

```
 audio (content/audio/twN.mp3)
   │  transcribe-episode.py            ElevenLabs Scribe (default, best quality)
   │  transcribe-episode-local.py      free local Whisper + pyannote (fallback, needs proofreading)
   ▼
 .work/transcripts/raw/twN.json         raw API response (ElevenLabs)            ┐ never edited
 .work/transcripts/local/twN-raw.json   raw words (local)                        ┘
 .work/transcripts/segments/twN.json    converted turns {speaker_N, start_time, text}
   │  sm.py / lsm.py   inspect speakers      map.py (apply-speaker-mapping.py)   name the speakers
   ▼
 .work/transcripts/segments/twN.json    turns with real names
   │  imp.py                            insert tw-transcript block into content/…/episode.txt
   ▼
 episode.txt  ──►  globalfix.py / textfix.py / rename_speaker.py / jinglefix.py   corrections on the block
              ──►  verify.py                      quality gate against the page metadata
              ──►  meta.py                        hosts/guests, participant stubs
              ──►  archive-transcripts.py         content/.transcripts/twN.json.gz (word-level, versioned)
```

## Where things live

| What                                                                                       | Location                                                    | Versioned                                |
| ------------------------------------------------------------------------------------------ | ----------------------------------------------------------- | ---------------------------------------- |
| Scripts, this README, skills                                                               | `scripts/transcripts/`, `.claude/skills/`                   | main repo                                |
| API keys (`ELEVENLABS_API_KEY`, `HF_TOKEN`)                                                | repository `.env`                                           | no (git-ignored)                         |
| Work data: raw responses, converted/mapped transcripts, local results, logs, draft backups | `.work/transcripts/{raw,segments,local,logs,drafts-backup}` | no (git-ignored, excluded from deploys)  |
| Local transcription virtualenv                                                             | `.work/venv`                                                | no                                       |
| Transcript block shown on the site                                                         | first block of `content/2_mediathek/…/episode.txt`          | content repo                             |
| Word-level archive (timestamps, raw speaker ids, labels)                                   | `content/.transcripts/twN.json.gz`                          | content repo (Kirby ignores dot folders) |

All paths are defined once in `lib/paths.py`. Nothing in this toolkit uses the legacy `migration/` folder.

## Setup

- **ElevenLabs:** put `ELEVENLABS_API_KEY=…` into `.env`. Pay-as-you-go, about 10 credits per audio minute.
- **Local (optional, free):** put `HF_TOKEN=…` (Hugging Face read token) into `.env`, accept the terms of
  `pyannote/speaker-diarization-3.1`, `pyannote/segmentation-3.0` and `pyannote/speaker-diarization-community-1`, then run
  `scripts/transcripts/setup-local.sh` once (needs Apple Silicon, python3.12, ffmpeg, Xcode command line tools).
- The spell checker used by `proofread.py` is compiled by `setup-local.sh` (`swiftc … spell.swift -o spell`, binary ignored).

## Script reference

### Transcription

- **`transcribe-episode.py N audio [--speakers K] [--language deu]`** — Sends the audio to ElevenLabs Scribe (`scribe_v2`,
  diarization, word timestamps) with `curl`. Writes the raw response to `.work/transcripts/raw/twN.json` and the converted
  turns to `.work/transcripts/segments/twN.json` (speakers still `speaker_0…`), and prints the first line of every speaker
  to help naming them. Fails with the API message on `quota_exceeded`. Forcing `--speakers` can break diarization — use only
  when the automatic result is wrong.
- **`transcribe-episode-local.py N audio [--speakers K] [--model REPO] [--out-dir DIR]`** — Free local pipeline: ffmpeg →
  pyannote diarization (own process) → `mlx-whisper` large-v3 with word timestamps (own process, so PyTorch memory is freed
  first) → speaker assignment per word. Detects stretches in another language (English interviews) per 10-second window
  and re-transcribes them in that language (`lib/lang_ranges.py`). Writes `.work/transcripts/local/twN.json` (+ `-raw.json`).
  Run it with `.work/venv/bin/python`, one process at a time (16 GB RAM).
- **`setup-local.sh`** — One-time setup of `.work/venv` (mlx-whisper, pyannote.audio) and the spell checker binary.
- **`prep.sh N`** — Copies a local result `.work/transcripts/local/twN.json` to `…/segments/twN.json` so it can be mapped.

### Speaker labelling

- **`sm.py N`** — Speaker overview of `.work/transcripts/segments/twN.json` (ElevenLabs result): per raw id the number of
  turns, characters and sample lines. Use it to decide who is who.
- **`lsm.py N`** — Same for a local result (`.work/transcripts/local/twN.json`).
- **`map.py N '{"speaker_0":"David",…}' ['{"<start-time prefix>":"Name"}']`** — Maps every raw speaker id to a name (exits with
  an error if an id is left unmapped), merges consecutive turns, and optionally relabels single turns by the prefix of their
  start time (e.g. `"00:18,3"`). Not idempotent: once names are applied a second run does not remap. Calls
  `apply-speaker-mapping.py`.
- **`apply-speaker-mapping.py N '{…}'`** — The core of `map.py`: renames speakers in `segments/twN.json`, drops empty turns,
  removes bare backchannels ("Ja.", "Mhm."), strips filler words and merges same-speaker turns (`lib/transcript_segments.py`).

### Import and corrections

- **`imp.py N [true|false|auto] [--replace]`** — Prepends the `tw-transcript` block built from `segments/twN.json` to the
  episode's `Blocks` in `episode.txt` (direct file edit, handles both `Blocks: [...]` and the value on the next line;
  timestamps become `H:MM:SS` without milliseconds). `auto` opens the transcript by default for short shownotes (< 1000
  characters), otherwise closed. `--replace` swaps an existing transcript block. Refuses to duplicate a block and prints
  `CHANGES` when the episode has a Panel draft (`_changes/`).
- **`globalfix.py [N …]`** — Applies the version-controlled list of known name/term corrections (Technikwürze, Webkrauts,
  Maciejewski, Grochtdreis, Ginader, …) to the imported block(s); default: every episode with a transcript. Add recurring
  corrections to its list.
- **`textfix.py N '{"old":"new"}'`** — Exact-substring replacements inside one episode's block (all occurrences), for
  episode-specific certain corrections.
- **`rename_speaker.py N old new`** — Renames a speaker label inside a published block.
- **`setspeaker.py N <timestamp> <speaker>`** — Relabels one segment of a published block (e.g. a screen-reader sample that was
  attributed to a host).
- **`swap_speakers.py N A B --from <timestamp> [--dry-run]`** — Swaps two speaker labels from the first segment with the given timestamp to the end of the block. Index based, so duplicate timestamps are safe. Use it when the diarization exchanged two speaker ids mid-episode (tw184: David ↔ Dirk Jesse from 06:08).
- **`jinglefix.py`** — Relabels pure jingle turns (intro/outro boilerplate) as `Einspieler` in all episodes.
- **`proofread.py [N …]`** — Flags suspicious words in local transcripts: words missing from the vocabulary of all
  ElevenLabs transcripts and rejected by the macOS spell checker (`spell.swift`), with context. Review output, then fix with
  `textfix.py`.
- **`spell.swift`** — Reads words from stdin and prints those NSSpellChecker rejects (`./spell [lang]`, default `de`).

### Quality gate and metadata

- **`verify.py N …`** — Checks an imported block against the episode page: raw `speaker_N` labels, labels that are neither a
  participant nor a standard label (`Einspieler`, `Hörer`, `Publikum`, `Interviewpartner`), participants without segments,
  empty/duplicated segments, backwards timestamps, passages that look English, jingles labelled as a person, known name
  misspellings. Exit status 1 on errors. Must be clean (or each warning explained) after every import.
- **`meta.py list | stub First Last slug TW<N> | add N H|G Name… | remove N H|G Name | guestmod Name`** — Participant helper:
  `list` prints uuid and title of all participants; `stub` creates an unlisted participant page (no numeric prefix, empty
  profile, TODO description); `add`/`remove` edit `Podcasterhosts` (H, "Team & Gastmoderation") or `Podcasterguests` (G, "Gäste") of an
  episode as `page://<uuid>` lists; `guestmod` gives a guest the extra role "Gastmoderation".

### Archive

- **`archive-transcripts.py [N …]`** — Writes `content/.transcripts/twN.json.gz` for each episode (default: all with raw
  data): words `[text, start, end, raw_speaker_id]`, audio events marked `"event"`, the raw-id → published-label mapping
  (derived by aligning with the published block) and the source (`elevenlabs` / `whisper-local`). Run after every import.

### Library modules (`lib/`)

- **`paths.py`** — Central paths and `env(key)` (reads secrets from the environment or `.env`).
- **`transcript_segments.py`** — Turn cleanup shared by the converters: filler removal, backchannel removal, same-speaker
  merge, long-paragraph wrapping.
- **`lang_ranges.py`** — Whisper-based language detection per 10-second window and re-transcription of foreign-language
  ranges.

## Data formats

- **Converted turns** (`segments/twN.json`): `{"segments": [{"speaker", "start_time": "MM:SS,mmm" | "H:MM:SS,mmm", "text"}]}`.
- **Block in `episode.txt`**: `{"type":"tw-transcript","id","isHidden":false,"content":{"headline":"Transkript","intro":"",
"initialstate":"true|false","hiderepeatedspeakersuntilchange":"false","segments":[{"speaker","timestamp","text"}]}}`.
- **Archive** (`content/.transcripts/twN.json.gz`): `{"episode","source","language","audio_duration_secs","speaker_labels":
{raw id: label},"words":[[text,start_s,end_s,raw_speaker_id], …]}`; audio events carry a fifth element `"event"`.

## Conventions and gotchas

- Labels are first names (`David`, `Marcel`); add a surname only to disambiguate (`Dirk Ginader`, `Dirk Jesse`). Jingles, songs
  and pre-produced clips are `Einspieler`, listener contributions `Hörer` or their first name, live audience `Publikum`,
  unnamed interviewees `Interviewpartner`. Never guess names.
- Correct only what is certain; unclear passages stay as transcribed.
- ElevenLabs is the default; local Whisper makes more word and name errors and loses phrases. Run only one Whisper process at
  a time.
- Editing `episode.txt` directly does not touch a Panel draft in `_changes/`; report drafts, never delete them unasked.
- Nothing here commits: the content repo is committed only on request.
