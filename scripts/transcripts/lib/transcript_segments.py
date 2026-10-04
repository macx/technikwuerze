"""Shared helpers for transcript segment post-processing.

A speaker change should only produce a new timestamped turn when someone
else actually starts talking. Consecutive segments from the same speaker
(an artifact of how diarization/ASR chunks audio) are merged into a single
turn, keeping the earliest timestamp and joining the text as paragraphs.

Bare backchannel acknowledgements ("Ja.", "Mhm.", ...) that interrupt the
other speaker's turn without adding content are dropped entirely, since in
a moderated conversation they carry no informational value and only
fragment the transcript into unnecessary speaker switches.
"""

import re

BACKCHANNEL_WORDS = {"ja", "mhm", "genau", "richtig", "ok", "okay"}

FILLER_RE = re.compile(r"[Ää]hm?\b")


def remove_filler_words(text):
    """Strip "äh"/"ähm" filler interjections and patch up the surrounding
    punctuation/capitalization so the sentence still reads naturally."""
    # "X, äh." / "X, äh!" / "X, äh?" (comma before, terminal punct after) -> "X."
    text = re.sub(r",\s*" + FILLER_RE.pattern + r"\s*(?=[.!?])", "", text)
    # "X äh." (no comma before, terminal punct after) -> "X."
    text = re.sub(r"\s+" + FILLER_RE.pattern + r"\s*(?=[.!?])", "", text)
    # "X, äh, Y" / "X, ähm, Y" -> "X, Y" (keep the original comma, drop the filler's own)
    text = re.sub(r",\s*" + FILLER_RE.pattern + r"\s*,\s*", ", ", text)
    # "X äh, Y" (no leading comma) -> "X Y"
    text = re.sub(r"(?<=\w)\s+" + FILLER_RE.pattern + r"\s*,\s*", " ", text)
    # Sentence-initial "Äh, X" / "Ähm, X" -> capitalize X
    text = re.sub(
        r"(^|[.!?]\s+)" + FILLER_RE.pattern + r",?\s+(\w)",
        lambda m: m.group(1) + m.group(2).upper(),
        text,
    )
    # Any remaining standalone fillers
    text = re.sub(r"\s*" + FILLER_RE.pattern + r"\s*", " ", text)
    # Cleanup: no space before punctuation, collapse double spaces
    text = re.sub(r"\s+([,.!?])", r"\1", text)
    text = re.sub(r"\s{2,}", " ", text)
    return text.strip()


def merge_consecutive_speakers(segments, speaker_key="speaker", text_key="text"):
    merged = []
    for seg in segments:
        if merged and merged[-1][speaker_key] == seg[speaker_key]:
            prev_text = merged[-1][text_key].strip()
            new_text = seg[text_key].strip()
            merged[-1][text_key] = "\n\n".join(t for t in (prev_text, new_text) if t)
        else:
            merged.append(dict(seg))
    return merged


def is_bare_backchannel(text, filler_words=BACKCHANNEL_WORDS):
    bare = re.sub(r"[^\w\s]", "", text).strip().lower()
    return bare in filler_words


def strip_bare_backchannel_turns(segments, text_key="text", filler_words=BACKCHANNEL_WORDS):
    return [seg for seg in segments if not is_bare_backchannel(seg[text_key], filler_words)]


def wrap_paragraph(paragraph, max_chars=500):
    """Split one paragraph of text into several at sentence boundaries,
    each at most max_chars long (best-effort — a single sentence longer
    than max_chars is kept whole rather than cut mid-sentence)."""
    if len(paragraph) <= max_chars:
        return [paragraph]

    sentences = re.split(r"(?<=[.!?])\s+", paragraph)
    chunks = []
    current = ""
    for sentence in sentences:
        if current and len(current) + 1 + len(sentence) > max_chars:
            chunks.append(current)
            current = sentence
        else:
            current = f"{current} {sentence}".strip()
    if current:
        chunks.append(current)
    return chunks


def split_long_paragraphs(text, max_chars=500):
    """Break a long monologue into readable paragraphs. Existing paragraph
    breaks (from merging same-speaker turns) are preserved as hard
    boundaries; only paragraphs still longer than max_chars get split
    further, at sentence boundaries."""
    paragraphs = text.split("\n\n")
    result = []
    for paragraph in paragraphs:
        result.extend(wrap_paragraph(paragraph, max_chars))
    return "\n\n".join(result)


def clean_segments(segments, speaker_key="speaker", text_key="text", max_paragraph_chars=500):
    """Standard post-processing: drop bare backchannel turns, strip "äh"/
    "ähm" filler words, merge consecutive same-speaker turns that became
    adjacent as a result, and break up long monologues into multiple
    paragraphs for readability."""
    segments = strip_bare_backchannel_turns(segments, text_key=text_key)
    segments = [dict(seg, **{text_key: remove_filler_words(seg[text_key])}) for seg in segments]
    segments = merge_consecutive_speakers(segments, speaker_key=speaker_key, text_key=text_key)
    for seg in segments:
        seg[text_key] = split_long_paragraphs(seg[text_key], max_paragraph_chars)
    return segments
