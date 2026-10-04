"""usage: map.py N '{"speaker_00":"David",...}' ['{"<start_time prefix>":"Name"}']  (run prep.sh N first; not idempotent)"""
import sys, json, subprocess, collections
from common import *
rebuild = '--rebuild' in sys.argv
args = [a for a in sys.argv[1:] if a != '--rebuild']
n = args[0]; m = args[1]; fixes = json.loads(args[2]) if len(args) > 2 else {}
if rebuild:
    import importlib.util
    spec = importlib.util.spec_from_file_location('te', ROOT + 'scripts/transcripts/transcribe-episode.py'); te = importlib.util.module_from_spec(spec); spec.loader.exec_module(te)
    json.dump({'segments': te.words_to_segments(json.load(open(f'{RAW}tw{n}.json'))['words'])}, open(f'{TRANS}tw{n}.json', 'w'), ensure_ascii=False)
r = subprocess.run(['python3', ROOT + 'scripts/transcripts/apply-speaker-mapping.py', n, m], capture_output=True, text=True, cwd=ROOT)
print(r.stdout.strip().split('\n')[-1], r.stderr[-200:])
p = f'{TRANS}tw{n}.json'
d = json.load(open(p)); seg = d['segments']
for k, v in fixes.items():
    hit = [s for s in seg if s['start_time'].startswith(k)]
    if not hit: print('MISSING', k)
    for s in hit: s['speaker'] = v
json.dump(d, open(p, 'w'), ensure_ascii=False)
cnt = collections.Counter(s['speaker'] for s in seg); print(cnt)
left = [k for k in cnt if k.startswith('speaker_')]
if left: print('!!! UNMAPPED SPEAKERS:', left); sys.exit(1)
