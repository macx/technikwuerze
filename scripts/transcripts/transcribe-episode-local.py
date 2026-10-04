#!/usr/bin/env python3
"""Local alternative to transcribe-episode.py: mlx-whisper (speech-to-text) plus
pyannote (speaker diarization), no API credits.

Usage:
    .work/venv/bin/python scripts/transcripts/transcribe-episode-local.py <episode_number> <audio_path> [--speakers N] [--model REPO] [--out-dir DIR]

Reads HF_TOKEN from the repository .env. Writes the same JSON shape as
transcribe-episode.py (raw words + "segments"), by default into
.work/transcripts/local/ so ElevenLabs results are never overwritten;
copy to .work/transcripts/segments/ (prep.sh) to feed map.py.
"""

import argparse
import importlib.util
import json
import os
import subprocess
import sys
import tempfile
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
sys.path.insert(0, str(Path(__file__).resolve().parent / "lib"))
import paths  # noqa: E402
DEFAULT_OUT = paths.LOCAL
DEFAULT_MODEL = "mlx-community/whisper-large-v3-mlx"

spec = importlib.util.spec_from_file_location("te", Path(__file__).with_name("transcribe-episode.py"))
te = importlib.util.module_from_spec(spec)
spec.loader.exec_module(te)


def load_env(key: str) -> str:
    return paths.env(key)


def to_wav(audio: Path, wav: Path) -> None:
    subprocess.run(
        ["ffmpeg", "-y", "-loglevel", "error", "-i", str(audio), "-ac", "1", "-ar", "16000", str(wav)],
        check=True,
    )


def run_whisper(wav: Path, model: str) -> list[dict]:
    import mlx_whisper

    result = mlx_whisper.transcribe(
        str(wav),
        path_or_hf_repo=model,
        language="de",
        word_timestamps=True,
        condition_on_previous_text=False,
        hallucination_silence_threshold=2.0,
        verbose=None,
    )
    words = []
    for seg in result["segments"]:
        for w in seg.get("words", []):
            words.append({"text": w["word"], "start": w["start"], "end": w["end"], "type": "word"})
    return words


def run_diarization(wav: Path, token: str, speakers: int | None) -> list[tuple[float, float, str]]:
    import torch
    from pyannote.audio import Pipeline

    os.environ["HF_TOKEN"] = token
    pipeline = Pipeline.from_pretrained("pyannote/speaker-diarization-3.1", token=token)
    device = "mps" if torch.backends.mps.is_available() else "cpu"
    pipeline.to(torch.device(device))
    kwargs = {"num_speakers": speakers} if speakers else {}
    output = pipeline(str(wav), **kwargs)
    annotation = getattr(output, "speaker_diarization", output)
    return [(t.start, t.end, spk) for t, _, spk in annotation.itertracks(yield_label=True)]


def assign_speakers(words: list[dict], turns: list[tuple[float, float, str]]) -> None:
    for w in words:
        mid = (w["start"] + w["end"]) / 2
        best, best_dist = None, None
        for start, end, spk in turns:
            if start <= mid <= end:
                best, best_dist = spk, 0
                break
            dist = min(abs(mid - start), abs(mid - end))
            if best_dist is None or dist < best_dist:
                best, best_dist = spk, dist
        w["speaker_id"] = (best or "SPEAKER_00").lower().replace("speaker_", "speaker_")


def run_stage(stage: str, args, wav: Path, work: Path) -> None:
    token = load_env("HF_TOKEN")
    if stage == "diarize":
        turns = run_diarization(wav, token, args.speakers)
        (work / "turns.json").write_text(json.dumps(turns))
    else:
        words = run_whisper(wav, args.model)
        sys.path.insert(0, str(Path(__file__).resolve().parent / "lib"))
        import lang_ranges

        words, ranges = lang_ranges.replace_foreign_words(words, wav, args.model)
        (work / "words.json").write_text(json.dumps(words))
        (work / "foreign.json").write_text(json.dumps(ranges))


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("episode_number", type=int)
    parser.add_argument("audio_path", type=Path)
    parser.add_argument("--speakers", type=int, default=None)
    parser.add_argument("--model", default=DEFAULT_MODEL)
    parser.add_argument("--out-dir", type=Path, default=DEFAULT_OUT)
    parser.add_argument("--stage", choices=["diarize", "whisper"], help="internal: run a single stage")
    parser.add_argument("--work-dir", type=Path, help="internal")
    args = parser.parse_args()

    if args.stage:
        run_stage(args.stage, args, args.work_dir / "audio.wav", args.work_dir)
        return

    args.out_dir.mkdir(parents=True, exist_ok=True)
    with tempfile.TemporaryDirectory() as tmp:
        work = Path(tmp)
        to_wav(args.audio_path, work / "audio.wav")
        # Separate processes so PyTorch (MPS) memory is released before MLX loads Whisper.
        for stage in ("diarize", "whisper"):
            print(f"Stage {stage} ...", flush=True)
            cmd = [sys.executable, __file__, str(args.episode_number), str(args.audio_path),
                   "--stage", stage, "--work-dir", str(work), "--model", args.model]
            if args.speakers:
                cmd += ["--speakers", str(args.speakers)]
            subprocess.run(cmd, check=True)
        turns = [tuple(t) for t in json.loads((work / "turns.json").read_text())]
        words = json.loads((work / "words.json").read_text())
        foreign = json.loads((work / "foreign.json").read_text())

    assign_speakers(words, turns)
    segments = te.words_to_segments(words)
    n = args.episode_number
    (args.out_dir / f"tw{n}-raw.json").write_text(json.dumps({"words": words}, ensure_ascii=False))
    (args.out_dir / f"tw{n}.json").write_text(json.dumps({"segments": segments}, ensure_ascii=False))
    if foreign:
        print("Foreign-language ranges re-transcribed:", foreign)
    print(f"Segment count: {len(segments)}")
    for spk, text in te.speaker_summary(segments):
        print(f'  {spk}: "{text}..."')


if __name__ == "__main__":
    main()
