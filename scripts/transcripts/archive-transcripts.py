#!/usr/bin/env python3
"""Archive the word-level transcript data of episodes in the content repo: content/.transcripts/tw<N>.json.gz
(Kirby ignores dot folders, like content/.db). One compact file per episode:

  {"episode": N, "source": "elevenlabs"|"whisper-local", "language": "de", "audio_duration_secs": 3600.0,
   "speaker_labels": {"speaker_0": "David", ...},          # raw id -> label used in the published transcript block
   "words": [[text, start_s, end_s, raw_speaker_id], ... , [text, start, end, raw_speaker_id, "event"]]}

The speaker labels are derived by aligning the words with the segments of the published tw-transcript block, so the
archive always reflects what is on the site. Usage: archive-transcripts.py [N ...]   (default: all episodes that have raw data)
"""
import collections, glob, gzip, json, re, sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
OUT = ROOT / 'content' / '.transcripts'
sys.path.insert(0, str(ROOT / 'scripts' / 'transcripts' / 'lib'))
import paths  # noqa: E402

RAW_EL = paths.RAW
RAW_LOCAL = paths.LOCAL
BL = re.compile(r'^Blocks:[ \t]*\n*(\[.*\])$', re.M)


def block_segments(n):
    f = glob.glob(str(ROOT / f'content/2_mediathek/*/*_tw{n}-*/episode.txt'))
    if len(f) != 1: return None
    m = BL.search(open(f[0], encoding='utf-8').read())
    for b in json.loads(m.group(1)) if m else []:
        if b['type'] == 'tw-transcript':
            out = []
            for s in b['content']['segments']:
                sec = sum(int(x) * 60 ** k for k, x in enumerate(reversed(s['timestamp'].split(':'))))
                out.append((sec, s['speaker']))
            return out
    return None


def archive(n):
    segs = block_segments(n)
    if not segs: return 'no transcript block'
    el, lo = RAW_EL / f'tw{n}.json', RAW_LOCAL / f'tw{n}-raw.json'
    if el.exists(): path, source = el, 'elevenlabs'
    elif lo.exists(): path, source = lo, 'whisper-local'
    else: return 'no raw data'
    d = json.load(open(path)); raw = d['words']
    words = []
    for w in raw:
        if w.get('type') == 'spacing' or not w.get('text', '').strip(): continue
        row = [w['text'], round(w['start'], 2), round(w['end'], 2), w.get('speaker_id')]
        if w.get('type') == 'audio_event': row.append('event')
        words.append(row)
    starts = [s for s, _ in segs]
    votes = collections.defaultdict(collections.Counter)
    import bisect
    for text, a, b, spk, *rest in words:
        if rest: continue
        i = max(0, bisect.bisect_right(starts, (a + b) / 2) - 1)
        votes[spk][segs[i][1]] += 1
    labels = {k: c.most_common(1)[0][0] for k, c in votes.items() if k}
    OUT.mkdir(exist_ok=True)
    payload = {'episode': int(n), 'source': source, 'language': d.get('language_code', 'de'), 'audio_duration_secs': d.get('audio_duration_secs'),
               'speaker_labels': labels, 'words': words}
    with gzip.open(OUT / f'tw{n}.json.gz', 'wt', encoding='utf-8', compresslevel=9) as fh:
        json.dump(payload, fh, ensure_ascii=False, separators=(',', ':'))
    return f'{source}, {len(words)} words, {len(labels)} speakers'


if __name__ == '__main__':
    eps = sys.argv[1:] or sorted({re.search(r'tw(\d+)', p.name).group(1) for p in list(RAW_EL.glob('tw*.json')) + list(RAW_LOCAL.glob('tw*-raw.json'))}, key=int)
    for n in eps: print(f'tw{n}:', archive(n))
