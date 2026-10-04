"""usage: textfix.py N '{"old":"new",...}'  - replace text inside the tw-transcript segments of episode N (exact substrings, all occurrences)"""
import sys, re, json, glob
from common import *
n = sys.argv[1]; reps = json.loads(sys.argv[2])
f = glob.glob(ROOT + f'content/2_mediathek/*/*_tw{n}-*/episode.txt')[0]
t = open(f, encoding='utf-8').read()
BL = re.compile(r'^Blocks:[ \t]*\n*(\[.*\])$', re.M)
m = BL.search(t); b = json.loads(m.group(1))
tr = [x for x in b if x['type'] == 'tw-transcript'][0]
cnt = {k: 0 for k in reps}
for s in tr['content']['segments']:
    for k, v in reps.items():
        c = s['text'].count(k)
        if c: s['text'] = s['text'].replace(k, v); cnt[k] += c
new = json.dumps(b, ensure_ascii=False, separators=(',', ':'))
open(f, 'w', encoding='utf-8').write(t[:m.start(1)] + new + t[m.end(1):])
print({k: c for k, c in cnt.items()}, '| not found:', [k for k, c in cnt.items() if c == 0])
