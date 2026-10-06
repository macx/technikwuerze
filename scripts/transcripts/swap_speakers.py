"""usage: swap_speakers.py N <speakerA> <speakerB> --from <timestamp> [--dry-run]  - swap two speaker labels in episode N's
published tw-transcript block from the first segment with timestamp <timestamp> (as shown in the block, e.g. 06:08) to the
end. Index based, so duplicate timestamps are safe. Use when the diarization exchanged two speaker ids mid-episode."""
import sys, re, json, glob
from common import *
args = sys.argv[1:]
dry = '--dry-run' in args
if dry: args.remove('--dry-run')
n, a, b = args[:3]
ts = args[args.index('--from') + 1]
f = glob.glob(ROOT + f'content/2_mediathek/*/*_tw{n}-*/episode.txt')[0]
t = open(f, encoding='utf-8').read(); BL = re.compile(r'^Blocks:[ \t]*\n*(\[.*\])$', re.M); m = BL.search(t); blocks = json.loads(m.group(1)); c = 0
for x in blocks:
    if x['type'] != 'tw-transcript': continue
    segs = x['content']['segments']
    start = next((i for i, s in enumerate(segs) if s['timestamp'] == ts), None)
    if start is None: sys.exit(f'no segment with timestamp {ts}')
    for s in segs[start:]:
        if s['speaker'] == a: s['speaker'] = b; c += 1
        elif s['speaker'] == b: s['speaker'] = a; c += 1
    print(f'swapped {a} <-> {b} in {len(segs) - start} segments from index {start} ({ts}), {c} relabeled')
if dry: sys.exit('dry run, nothing written')
open(f, 'w', encoding='utf-8').write(t[:m.start(1)] + json.dumps(blocks, ensure_ascii=False, separators=(',', ':')) + t[m.end(1):])
