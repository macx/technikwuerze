"""usage: rename_speaker.py N old new  - rename a speaker label in episode N's tw-transcript"""
import sys, re, json, glob
from common import *
n, old, new = sys.argv[1:4]
f = glob.glob(ROOT + f'content/2_mediathek/*/*_tw{n}-*/episode.txt')[0]
t = open(f, encoding='utf-8').read(); BL = re.compile(r'^Blocks:[ \t]*\n*(\[.*\])$', re.M); m = BL.search(t); b = json.loads(m.group(1)); c = 0
for x in b:
    if x['type'] == 'tw-transcript':
        for s in x['content']['segments']:
            if s['speaker'] == old: s['speaker'] = new; c += 1
open(f, 'w', encoding='utf-8').write(t[:m.start(1)] + json.dumps(b, ensure_ascii=False, separators=(',', ':')) + t[m.end(1):]); print('renamed', c)
