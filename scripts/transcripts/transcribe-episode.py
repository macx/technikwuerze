#!/usr/bin/env python3
"""Transcribe a Technikwürze episode via the ElevenLabs Speech-to-Text API
and convert the result into the JSON shape the Kirby tw-transcript importer
plugin accepts (a top-level "segments" array).

Usage:
    python3 scripts/transcripts/transcribe-episode.py <episode_number> <audio_path> [--speakers N]

Reads the API key from the repository .env (ELEVENLABS_API_KEY=...).
Writes:
    .work/transcripts/raw/tw<N>.json            (raw ElevenLabs response)
    .work/transcripts/segments/tw<N>.json       (converted "segments" JSON, input of map.py)

Prints a speaker summary (first line per speaker_id, in order of first
appearance) so the real names can be mapped before import.
"""

import argparse
import json
import subprocess
import sys
import tempfile
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent / "lib"))
from transcript_segments import clean_segments  # noqa: E402

ROOT = Path(__file__).resolve().parents[2]
import paths  # noqa: E402

RAW_DIR = paths.RAW
OUT_DIR = paths.SEGMENTS

API_URL = "https://api.elevenlabs.io/v1/speech-to-text"
MODEL_ID = "scribe_v2"


def load_api_key() -> str:
    return paths.env("ELEVENLABS_API_KEY")


def transcribe(audio_path: Path, api_key: str, num_speakers: int | None, language_code: str) -> dict:
    with tempfile.NamedTemporaryFile(suffix=".json") as body_out:
        cmd = [
            "curl",
            "--silent",
            "--show-error",
            "--fail-with-body",
            "--max-time",
            "1800",
            "-X",
            "POST",
            API_URL,
            "-H",
            f"xi-api-key: {api_key}",
            "-F",
            f"model_id={MODEL_ID}",
            "-F",
            f"language_code={language_code}",
            "-F",
            "diarize=true",
            "-F",
            "timestamps_granularity=word",
            "-F",
            f"file=@{audio_path}",
        ]
        if num_speakers:
            cmd += ["-F", f"num_speakers={num_speakers}"]
        cmd += ["-o", body_out.name]

        result = subprocess.run(cmd, capture_output=True, text=True)
        if result.returncode != 0:
            raise SystemExit(
                f"curl failed (exit {result.returncode}): {result.stderr}\n{Path(body_out.name).read_text(errors='replace')}"
            )
        return json.loads(Path(body_out.name).read_text())


def seconds_to_timestamp(seconds: float) -> str:
    total_ms = round(seconds * 1000)
    h, rem = divmod(total_ms, 3_600_000)
    m, rem = divmod(rem, 60_000)
    s, ms = divmod(rem, 1000)
    if h:
        return f"{h}:{m:02d}:{s:02d},{ms:03d}"
    return f"{m:02d}:{s:02d},{ms:03d}"


def words_to_segments(words: list[dict]) -> list[dict]:
    segments = []
    current_speaker = None
    current_text = []
    current_start = None

    def flush():
        if current_text:
            segments.append(
                {
                    "speaker": current_speaker or "Speaker 1",
                    "start_time": seconds_to_timestamp(current_start or 0.0),
                    "text": "".join(current_text).strip(),
                }
            )

    for w in words:
        if w.get("type") == "audio_event":
            continue
        speaker = w.get("speaker_id") or "speaker_0"
        if speaker != current_speaker:
            flush()
            current_speaker = speaker
            current_text = []
            current_start = w.get("start")
        current_text.append(w.get("text", ""))

    flush()
    return clean_segments(segments, text_key="text")


def speaker_summary(segments: list[dict]) -> list[tuple[str, str]]:
    seen = {}
    for seg in segments:
        if seg["speaker"] not in seen:
            seen[seg["speaker"]] = seg["text"][:80]
    return list(seen.items())


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("episode_number", type=int)
    parser.add_argument("audio_path", type=Path)
    parser.add_argument("--speakers", type=int, default=None, help="expected number of speakers")
    parser.add_argument("--language", default="deu")
    args = parser.parse_args()

    if not args.audio_path.exists():
        raise SystemExit(f"Audio file not found: {args.audio_path}")

    RAW_DIR.mkdir(parents=True, exist_ok=True)
    OUT_DIR.mkdir(parents=True, exist_ok=True)

    api_key = load_api_key()

    print(f"Transcribing tw{args.episode_number} ({args.audio_path.name}) ...", file=sys.stderr)
    raw = transcribe(args.audio_path, api_key, args.speakers, args.language)

    raw_path = RAW_DIR / f"tw{args.episode_number}.json"
    raw_path.write_text(json.dumps(raw, ensure_ascii=False, indent=2))

    segments = words_to_segments(raw.get("words", []))
    out_path = OUT_DIR / f"tw{args.episode_number}.json"
    out_path.write_text(json.dumps({"segments": segments}, ensure_ascii=False, indent=2))

    print(f"Raw response:    {raw_path}")
    print(f"Segments (plugin shape): {out_path}")
    print(f"Segment count: {len(segments)}")
    print("\nSpeaker summary (first appearance order):")
    for speaker_id, preview in speaker_summary(segments):
        print(f"  {speaker_id}: \"{preview}...\"")


if __name__ == "__main__":
    main()
