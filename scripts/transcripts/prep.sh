#!/bin/bash
# Copy a local (Whisper) transcript into .work/transcripts/segments/ so map.py can label its speakers.
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
mkdir -p "$ROOT/.work/transcripts/segments"
cp "$ROOT/.work/transcripts/local/tw$1.json" "$ROOT/.work/transcripts/segments/tw$1.json"
