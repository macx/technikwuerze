"""Flag suspicious words/speakers in 'Whisper lokal' transcripts.
Vocabulary = all words used in ElevenLabs transcripts + participant names.
usage: proofread.py [N ...]   (default: all 'Whisper lokal' rows)"""
import re, json, glob, sys, collections, subprocess, os
from common import *
from pathlib import Path
ROOT_PATH = Path(ROOT)

BL = re.compile(r'^Blocks:[ \t]*\n*(\[.*\])$', re.M)
TOK = re.compile(r"[A-Za-zÄÖÜäöüß][A-Za-zÄÖÜäöüß'-]*")

def transcript(n):
    f = glob.glob(ROOT + f'content/2_mediathek/*/*_tw{n}-*/episode.txt')[0]
    b = json.loads(BL.search(open(f, encoding='utf-8').read()).group(1))
    return [x for x in b if x['type'] == 'tw-transcript'][0]['content']['segments']

def rows():
    """(elevenlabs_episodes, local_episodes) from the 'source' field of the archive files."""
    import gzip
    el, lo = [], []
    for f in sorted((ROOT_PATH / 'content' / '.transcripts').glob('tw*.json.gz')):
        src = json.load(gzip.open(f, 'rt', encoding='utf-8')).get('source')
        (lo if src == 'whisper-local' else el).append(re.search(r'tw(\d+)', f.name).group(1))
    return el, lo

def vocab(el):
    c = collections.Counter()
    for n in el:
        try:
            for s in transcript(n): c.update(w.lower() for w in TOK.findall(s['text']))
        except Exception: pass
    for f in glob.glob(ROOT + 'content/3_teilnehmende/*/participant.txt'):
        t = open(f, encoding='utf-8').read()
        c.update(w.lower() for w in TOK.findall(re.search(r'^Title:.*$', t, re.M).group(0)))
    return c

def misspelled(words):
    parts = sorted({p for w in words for p in re.split(r"-", w) if len(p) > 2})
    r = subprocess.run([os.path.dirname(__file__) + '/spell'], input='\n'.join(parts), capture_output=True, text=True)
    bad = set(r.stdout.split())
    return {w for w in words if any(p in bad for p in re.split(r"-", w))}

if __name__ == '__main__':
    el, lo = rows()
    V = vocab(el)
    targets = sys.argv[1:] or lo
    known_speakers = {'David', 'Einspieler', 'Hörer', 'Publikum'}
    for n in targets:
        seg = transcript(n)
        spk = collections.Counter(s['speaker'] for s in seg)
        flagged = collections.Counter(); ctx = {}; orig = {}
        for s in seg:
            for w in TOK.findall(s['text']):
                lw = w.lower().strip("'-")
                if lw and V[lw] == 0 and len(lw) > 2:
                    flagged[lw] += 1
                    orig.setdefault(lw, w.strip("'-"))
                    if lw not in ctx:
                        i = s['text'].find(w); ctx[lw] = (s['timestamp'], s['speaker'], s['text'][max(0, i - 32):i + len(w) + 32].replace('\n', ' '))
        bad_orig = misspelled(list(set(orig.values())))
        bad = {k for k, v in orig.items() if v in bad_orig}
        print(f'=== tw{n}: speakers {dict(spk)} | {len(bad)} suspicious word types')
        for w, c in flagged.most_common():
            if w not in bad: continue
            print(f'   {w} x{c}  [{ctx[w][0]} {ctx[w][1]}] …{ctx[w][2]}…')
