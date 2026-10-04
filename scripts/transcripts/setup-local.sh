#!/bin/bash
# One-time setup for the free local transcription (Whisper + pyannote) and the spell checker. Needs Apple Silicon,
# python3.12, ffmpeg, Xcode command line tools and HF_TOKEN in the repository .env (Hugging Face read token; accept the
# terms of pyannote/speaker-diarization-3.1, pyannote/segmentation-3.0 and pyannote/speaker-diarization-community-1).
set -e
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$ROOT"
mkdir -p .work
[ -d .work/venv ] || /opt/homebrew/bin/python3.12 -m venv .work/venv
.work/venv/bin/pip install -q mlx-whisper pyannote.audio
swiftc -O scripts/transcripts/spell.swift -o scripts/transcripts/spell
echo "setup done"
