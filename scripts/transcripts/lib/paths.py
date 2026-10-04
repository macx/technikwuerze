"""Central paths of the transcript toolkit. Nothing here lives in `migration/`.

- secrets:      <repo>/.env                 (ELEVENLABS_API_KEY, HF_TOKEN; git-ignored)
- work data:    <repo>/.work/transcripts/   (git-ignored): raw/ (ElevenLabs responses), segments/ (converted + speaker-mapped),
                local/ (Whisper results), logs/, drafts-backup/
- local venv:   <repo>/.work/venv           (git-ignored)
- archive:      <repo>/content/.transcripts (versioned in the content repo, see archive-transcripts.py)
"""
import os
from pathlib import Path

ROOT = Path(__file__).resolve().parents[3]
WORK = ROOT / ".work" / "transcripts"
RAW = WORK / "raw"
SEGMENTS = WORK / "segments"
LOCAL = WORK / "local"
LOGS = WORK / "logs"
DRAFTS_BACKUP = WORK / "drafts-backup"
VENV = ROOT / ".work" / "venv"
ARCHIVE = ROOT / "content" / ".transcripts"
ENV_FILE = ROOT / ".env"


def env(key: str) -> str:
    """Read a secret from the process environment or the repository .env file."""
    if os.environ.get(key):
        return os.environ[key]
    if ENV_FILE.exists():
        for line in ENV_FILE.read_text().splitlines():
            if line.startswith(key + "="):
                return line.split("=", 1)[1].strip()
    raise SystemExit(f"{key} not found in the environment or {ENV_FILE}")
