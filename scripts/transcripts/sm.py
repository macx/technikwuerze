"""usage: sm.py N - speaker overview of .work/transcripts/segments/twN.json (ElevenLabs result): segments, characters and sample lines per raw speaker id."""
import json, sys
from common import *
seg = json.load(open(f'{TRANS}tw{sys.argv[1]}.json'))['segments']; seen = {}
for i, s in enumerate(seg): seen.setdefault(s['speaker'], []).append(i)
for k, v in seen.items():
    print(k, len(v), sum(len(seg[i]['text']) for i in v))
    for i in v[:3] + (v[-1:] if len(v) > 3 else []): print('   ', i, seg[i]['start_time'][:7], seg[i]['text'][:130].replace('\n', ' '))
