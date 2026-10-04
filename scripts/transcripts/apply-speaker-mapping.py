#!/usr/bin/env python3
"""Apply a raw-speaker-id -> real-name mapping to a converted transcript,
drop empty-text segments, and merge consecutive same-speaker turns (a
speaker change should only produce a new timestamped turn when someone
else actually starts talking).

Usage:
    python3 scripts/transcripts/apply-speaker-mapping.py <episode_number> '{"speaker_0":"David","speaker_1":"Jörg"}'

Edits .work/transcripts/segments/tw<N>.json in place.
"""

import json
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent / "lib"))
from transcript_segments import clean_segments  # noqa: E402

ROOT = Path(__file__).resolve().parents[2]
import paths  # noqa: E402

TRANSCRIPTS_DIR = paths.SEGMENTS


def main():
    if len(sys.argv) != 3:
        raise SystemExit(__doc__)

    episode_number = sys.argv[1]
    mapping = json.loads(sys.argv[2])

    path = TRANSCRIPTS_DIR / f"tw{episode_number}.json"
    data = json.loads(path.read_text())

    segments = data["segments"]
    for seg in segments:
        seg["speaker"] = mapping.get(seg["speaker"], seg["speaker"])

    segments = [s for s in segments if s["text"].strip()]
    segments = clean_segments(segments, text_key="text")

    data["segments"] = segments
    path.write_text(json.dumps(data, ensure_ascii=False, indent=2))

    print(f"{path}: {len(segments)} segments")
    print("Speakers:", sorted(set(s["speaker"] for s in segments)))


if __name__ == "__main__":
    main()
