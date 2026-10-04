"""Shared constants of the small helper scripts: repository root (with trailing slash) and the work directories."""
from pathlib import Path

ROOT = str(Path(__file__).resolve().parents[2]) + '/'
LOCAL = ROOT + '.work/transcripts/local/'
TRANS = ROOT + '.work/transcripts/segments/'
RAW = ROOT + '.work/transcripts/raw/'
