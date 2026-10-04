"""Detect non-German stretches in an audio file with Whisper language ID, so they can be
re-transcribed in the right language instead of being turned into garbled German."""

WINDOW_S = 10
MIN_PROB = 0.6
PROMPT = "Technikwürze, Webkrauts, Web-Standards podcast."


def detect_language_windows(wav_path, model_repo, native="de"):
    """Return [(start_s, end_s, lang_code)] per 30 s window (consecutive same-language windows kept separate)."""
    import mlx.core as mx
    from mlx_whisper.audio import N_FRAMES, SAMPLE_RATE, load_audio, log_mel_spectrogram, pad_or_trim
    from mlx_whisper.decoding import detect_language
    from mlx_whisper.tokenizer import get_tokenizer
    from mlx_whisper.transcribe import ModelHolder

    model = ModelHolder.get_model(model_repo, mx.float16)
    tokenizer = get_tokenizer(model.is_multilingual, num_languages=model.num_languages, language=native, task="transcribe")
    audio = load_audio(str(wav_path))
    step = WINDOW_S * SAMPLE_RATE
    min_len = 3 * SAMPLE_RATE
    out = []
    for start in range(0, len(audio), step):
        chunk = audio[start : start + step]
        if len(chunk) < min_len:
            break
        mel = log_mel_spectrogram(chunk, n_mels=model.dims.n_mels)
        mel = pad_or_trim(mel, N_FRAMES, axis=-2).astype(mx.float16)
        _, probs = detect_language(model, mel, tokenizer)
        probs = probs if isinstance(probs, dict) else probs[0]
        lang, p = max(probs.items(), key=lambda kv: kv[1])
        out.append((start / SAMPLE_RATE, min(start + step, len(audio)) / SAMPLE_RATE, lang if p >= MIN_PROB else native, p))
    return out


def foreign_ranges(windows, native="de", min_windows=6, pad_s=0):
    """Merge consecutive windows of the same foreign language; keep runs of at least min_windows."""
    ranges, cur = [], None
    for start, end, lang, _p in windows:
        if lang != native and cur and cur[2] == lang and abs(cur[1] - start) < 1e-6:
            cur[1] = end
        elif lang != native:
            if cur:
                ranges.append(tuple(cur))
            cur = [start, end, lang]
        else:
            if cur:
                ranges.append(tuple(cur))
            cur = None
    if cur:
        ranges.append(tuple(cur))
    return [(max(0, s - pad_s), e + pad_s, l) for s, e, l in ranges if (e - s) >= min_windows * WINDOW_S - 1]


def replace_foreign_words(words, wav_path, model_repo, native="de", log=print):
    """Re-transcribe stretches whose spoken language is not `native` in their own language and
    swap the (garbled) native-language words for them. Returns (words, ranges)."""
    import mlx_whisper
    from mlx_whisper.audio import SAMPLE_RATE, load_audio

    windows = detect_language_windows(wav_path, model_repo, native)
    ranges = foreign_ranges(windows, native)
    if not ranges:
        return words, []
    audio = load_audio(str(wav_path))
    for start, end, lang in ranges:
        log(f"Foreign language {lang}: {start:.0f}-{end:.0f}s")
        result = mlx_whisper.transcribe(
            audio[int(start * SAMPLE_RATE) : int(end * SAMPLE_RATE)],
            path_or_hf_repo=model_repo,
            language=lang,
            initial_prompt=PROMPT,
            word_timestamps=True,
            condition_on_previous_text=False,
            hallucination_silence_threshold=2.0,
            verbose=None,
        )
        new = [
            {"text": w["word"], "start": w["start"] + start, "end": w["end"] + start, "type": "word"}
            for seg in result["segments"]
            for w in seg.get("words", [])
        ]
        words = [w for w in words if not (start <= (w["start"] + w["end"]) / 2 < end)] + new
    words.sort(key=lambda w: w["start"])
    return words, ranges
