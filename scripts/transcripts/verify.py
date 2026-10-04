"""Quality gate for an imported transcript.  usage: verify.py N [N ...]
Checks the tw-transcript block of episode N against the episode's participants and prints errors (must fix) and
warnings (review): raw speaker ids, labels that are neither a participant nor a standard label, participants without
segments, empty/duplicated/non-monotonic segments, stretches that look English in a German episode, jingles labelled as
a person, name spellings that globalfix.py knows as errors."""
import re, json, glob, sys, collections
from common import *

BL = re.compile(r'^Blocks:[ \t]*\n*(\[.*\])$', re.M)
STANDARD = {'Einspieler', 'Hörer', 'Publikum', 'Interviewpartner'}
EN_WORDS = set('the and that with this have for you are was but not they what there about which would from your will just like when been'.split())
DE_WORDS = set('der die das und ist nicht ein eine ich wir auch aber dass mit von den für sich auf wie noch dann schon oder wenn sind'.split())


def participants():
    out = {}
    for f in glob.glob(ROOT + 'content/3_teilnehmende/*/participant.txt'):
        t = open(f, encoding='utf-8').read()
        title = re.search(r'^Title:[ \t]*(.*)$', t, re.M).group(1).strip()
        out[re.search(r'^Uuid:[ \t]*(\S+)', t, re.M).group(1)] = title
    return out


def check(n):
    f = glob.glob(ROOT + f'content/2_mediathek/*/*tw{n}-*/episode.txt')
    if len(f) != 1: return [f'ERROR no unique episode.txt for tw{n}'], []
    t = open(f[0], encoding='utf-8').read(); P = participants()
    errors, warnings = [], []
    m = BL.search(t)
    if not m: return ['ERROR Blocks field not parsable'], []
    blocks = json.loads(m.group(1)); tr = [b for b in blocks if b['type'] == 'tw-transcript']
    if len(tr) != 1: return [f'ERROR {len(tr)} transcript blocks (expected 1)'], []
    if blocks[0]['type'] != 'tw-transcript': warnings.append('transcript block is not the first block')
    segs = tr[0]['content']['segments']
    if not segs: return ['ERROR transcript has no segments'], []
    ids = {k: [] for k in ('Podcasterhosts', 'Podcasterguests')}
    for k in ids:
        mm = re.search(r'^' + k + r':.*?\n\n----', t, re.M | re.S | re.I)
        ids[k] = [P.get(u, u) for u in re.findall(r'page://(\w+)', mm.group(0))] if mm else []
    people = ids['Podcasterhosts'] + ids['Podcasterguests']
    ALIAS = {'Ben': 'Benjamin', 'Chris': 'Christian', 'Tom': 'Thomas', 'Tomas': 'Thomas', 'Eric': 'Erik', 'Erik': 'Eric'}
    def forms(p):
        w = p.split(); out = {' '.join(w[:k]) for k in range(1, len(w) + 1)}
        return out | {ALIAS[x] for x in list(out) if x in ALIAS} | {k for k, v in ALIAS.items() if v in out}
    allowed = set(STANDARD)
    for p in people: allowed |= forms(p)
    spk = collections.Counter(s['speaker'] for s in segs)
    for s in spk:
        if re.match(r'(?i)speaker[ _]?\d*$', s): errors.append(f'ERROR raw speaker label "{s}" ({spk[s]} segments)')
        elif s not in allowed and not re.match(r'Sprecher \d+$', s):
            warnings.append(f'label "{s}" ({spk[s]} segments) is not a participant of the page: listener/audience/guest missing in metadata?')
    for p in people:
        if not any(l in forms(p) or p.split()[0] == l.split()[0] or ALIAS.get(l.split()[0]) == p.split()[0] for l in spk):
            warnings.append(f'participant "{p}" has no segments (not in the audio, or labelled differently?)')
    prev = -1; seen = set(); english = []
    for i, s in enumerate(segs):
        parts = s['timestamp'].split(':'); sec = sum(int(x) * 60 ** k for k, x in enumerate(reversed(parts)))
        if sec + 2 < prev: warnings.append(f'timestamp goes backwards at segment {i} ({s["timestamp"]})')
        prev = max(prev, sec)
        if not s['text'].strip(): errors.append(f'ERROR empty segment {i} at {s["timestamp"]}')
        if len(s['text']) > 60 and s['text'].count(' ') < len(s['text']) / 25: errors.append(f'ERROR segment at {s["timestamp"]} has almost no spaces (broken text): "{s["text"][:50]}..."')
        key = s['text'][:80]
        if len(key) > 40 and key in seen and s['speaker'] != 'Einspieler': warnings.append(f'duplicated text at {s["timestamp"]}: "{key[:50]}..."')
        seen.add(key)
        words = re.findall(r"[a-zäöüß']+", s['text'].lower())
        if len(words) > 40:
            en = sum(w in EN_WORDS for w in words); de = sum(w in DE_WORDS for w in words)
            if en > de * 2 and en > 6 and s['speaker'] not in ('Einspieler',): english.append((s['timestamp'], s['speaker']))
        if s['speaker'] != 'Einspieler' and re.match(r'(Mehr Qualität im Webdesign|Herzlich willkommen zu Technikwürze, eurem|Weitere Informationen und die Links)', s['text']) and len(s['text']) < 420:
            warnings.append(f'{s["timestamp"]}: jingle labelled "{s["speaker"]}" (run jinglefix.py)')
    if english:
        who = collections.Counter(sp for _, sp in english)
        warnings.append(f'{len(english)} long passages look English (first at {english[0][0]}; speakers {dict(who)}) - expected only for English interviews/episodes')
    text = ' '.join(s['text'] for s in segs)
    for bad in ('Technikwitze', 'Technikwirtze', 'Webcrowds', 'Webcouts', 'Maczewski', 'Matziewski', 'Grochdreis', 'Technik-Würzel', 'Technik würze', 'speaker_'):
        if bad in text: warnings.append(f'known misspelling "{bad}" present (run globalfix.py)')
    warnings.append(f'INFO {len(segs)} segments, speakers {dict(spk)}, hosts {ids["Podcasterhosts"]}, guests {ids["Podcasterguests"]}')
    return errors, warnings


if __name__ == '__main__':
    rc = 0
    for n in sys.argv[1:]:
        e, w = check(n)
        print(f'=== tw{n}: {len(e)} errors, {len([x for x in w if not x.startswith("INFO")])} warnings')
        for x in e + w: print('  ' + x)
        rc |= bool(e)
    sys.exit(rc)
