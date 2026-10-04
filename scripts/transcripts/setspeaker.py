"""usage: setspeaker.py N <timestamp> <speaker>  - relabel the single segment of episode N's published transcript block whose
timestamp equals <timestamp> (as shown in the block, e.g. 12:36 or 1:08:56)."""
import sys, re, json, glob
from common import *
n, ts, name = sys.argv[1:4]
f = glob.glob(ROOT + f'content/2_mediathek/*/*_tw{n}-*/episode.txt')[0]
t = open(f, encoding='utf-8').read(); BL = re.compile(r'^Blocks:[ \t]*\n*(\[.*\])$', re.M); m = BL.search(t); b = json.loads(m.group(1)); c = 0
for x in b:
    if x['type'] == 'tw-transcript':
        for s in x['content']['segments']:
            if s['timestamp'] == ts: print('was', s['speaker'], '->', name, '|', s['text'][:60]); s['speaker'] = name; c += 1
open(f, 'w', encoding='utf-8').write(t[:m.start(1)] + json.dumps(b, ensure_ascii=False, separators=(',', ':')) + t[m.end(1):])
print('relabeled', c)
