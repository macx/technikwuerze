"""Label pure jingle segments (intro/outro boilerplate) as 'Einspieler' in all episodes' transcripts."""
import re, json, glob, sys
from common import *
BL = re.compile(r'^Blocks:[ \t]*\n*(\[.*\])$', re.M)
START = ('Mehr Qualität im Webdesign durch Webstandards', 'Herzlich willkommen zu Technikwürze, eurem Design', 'Weitere Informationen und die Links zur Sendung', 'Beteiligt euch an der Diskussion im Blog')
total = 0
for f in glob.glob(ROOT + 'content/2_mediathek/*/*/episode.txt'):
    t = open(f, encoding='utf-8').read(); m = BL.search(t)
    if not m: continue
    b = json.loads(m.group(1)); ch = 0
    for x in b:
        if x['type'] != 'tw-transcript': continue
        for s in x['content']['segments']:
            if s['speaker'] != 'Einspieler' and s['text'].startswith(START) and len(s['text']) < 420:
                s['speaker'] = 'Einspieler'; ch += 1
                s['text'] = s['text'].replace('Webkrauts DE', 'webkrauts.de').replace('Webkrauts.de', 'webkrauts.de')
    if ch:
        open(f, 'w', encoding='utf-8').write(t[:m.start(1)] + json.dumps(b, ensure_ascii=False, separators=(',', ':')) + t[m.end(1):])
        print(re.search(r'_tw(\d+)-', f).group(1), ch); total += ch
print('segments relabeled', total)
