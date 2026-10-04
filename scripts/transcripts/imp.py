"""usage: imp.py N [true|false|auto] [--replace]  - prepend tw-transcript block to episode.txt Blocks (direct file edit)"""
import json, sys, glob, re, uuid, os
from common import *
replace = '--replace' in sys.argv
args = [a for a in sys.argv[1:] if a != '--replace']
n = args[0]; state = args[1] if len(args) > 1 else None
f = glob.glob(ROOT + f'content/2_mediathek/*/*tw{n}-*/episode.txt'); assert len(f) == 1, f
f = f[0]
print(f, 'CHANGES' if os.path.exists(os.path.dirname(f) + '/_changes/episode.txt') else 'nochanges')
t = open(f, encoding='utf-8').read()
m = re.search(r'^Blocks:[ \t]*\n*(\[.*\])$', t, re.M); assert m
blocks = json.loads(m.group(1))
if replace: blocks = [b for b in blocks if b['type'] != 'tw-transcript']
if any(b['type'] == 'tw-transcript' for b in blocks): sys.exit('already has transcript')
notes = sum(len(b['content'].get('text', '')) for b in blocks if b['type'] == 'markdown')
if state == 'auto': state = 'true' if notes < 1000 else 'false'
if state is None: print('shownotes chars', notes); sys.exit()
segs = json.load(open(f'{TRANS}tw{n}.json'))['segments']
out = [{"speaker": s['speaker'], "timestamp": s['start_time'].split(',')[0], "text": s['text']} for s in segs]
blk = {"type": "tw-transcript", "id": str(uuid.uuid4()), "isHidden": False, "content": {"headline": "Transkript", "intro": "", "initialstate": state, "hiderepeatedspeakersuntilchange": "false", "segments": out}}
new = json.dumps([blk] + blocks, ensure_ascii=False, separators=(',', ':'))
open(f, 'w', encoding='utf-8').write(t[:m.start(1)] + new + t[m.end(1):])
print('written', len(out), 'segments')
