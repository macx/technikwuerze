"""Apply known Whisper name/term corrections to the tw-transcript of the given episodes (default: all 'Whisper lokal')."""
import sys, re, json, glob
from common import *
import proofread as p
BL = re.compile(r'^Blocks:[ \t]*\n*(\[.*\])$', re.M)
R = [
 (r'(?i)\bweb ?(?:cou?lls?|couts|calls|fotz|crops|crowds?|crauts?|grauds?|krautanker|krauts|crouts?)\b', 'Webkrauts'),
 (r'(?i)\bweb ?kraut\b', 'Webkraut'),
 (r'\bWebcouts-', 'Webkrauts-'),
 (r'\b[Tt]echnik(?:witzer?|würzel\w*|wirtze|wuerze|würtze|würzen)\b', 'Technikwürze'),
 (r'\b[Tt]echnik-[Kk]ürze\b', 'Technikwürze'), (r'\bTechnik[ -]?W[öo]r[a-zß]+\b', 'Technikwürze'),
 (r'\bMulti-?m[äa]deltreff\b|\bMultimediatreffen\b', 'Multimediatreff'),
 (r'\bMarczewski\b|\bMalczewski\b|\bMatziewski\b|\bMaczewski\b|\bMaceski\b|\bMatieski\b|\bMacierski\b|\bMarzeski\b|\bMatiescki\b|\bMaciewski\b', 'Maciejewski'),
 (r'\bSeven-Load\b|\bSeven Load\b', 'Sevenload'), (r'\bMeckerhecke\b|\bMeckerrecke\b', 'Meckerecke'),
 (r'\bJackery\b|\bJcrayu\b', 'jQuery'), (r'\bJehuda Katz\b', 'Yehuda Katz'),
 (r'\bGr?o?c?h?t?dreist?\b|\bKrochdreis\b|\bGrochdreis\b', 'Grochtdreis'),
 (r'\bTechnik-W[üu]rzel\b','Technikwürze'),
 (r'\bG[ie]nn?ader\b|\bGnader\b|\bKochdreis\b|\bGinn?ader\b|\bGennader\b', 'Ginader'),
 (r'\bMelanchton\b|\bMillianchton\b|\bMiljanjton\b', 'Melanchthon'),
 (r'\bThomas Kaspers\b|\bThomas Caspers\b','Tomas Caspers'), (r'\bErik Eggert\b|\bEric Eckert\b|\bErik Eckert\b','Eric Eggert'), (r'\bGerrit (?:von|van) Ark?en\b','Gerrit van Aaken'),
 (r'\bStefan Nietzsche\b|\bStephan Nietzsche\b|\bStefan Nietzsch\b','Stefan Nitzsche'), (r'\bNils Boker\b|\bNils Poker\b','Nils Pooker'),
 (r'\b[Tt]echnikwitzesendung\b','Technikwürze-Sendung'),
 (r'\bRenderring\b', 'Rendering'), (r'\bVergebnis\b', 'Ergebnis'),
]
def apply(n):
    f = glob.glob(ROOT + f'content/2_mediathek/*/*_tw{n}-*/episode.txt')[0]
    t = open(f, encoding='utf-8').read(); m = BL.search(t); b = json.loads(m.group(1))
    tr = [x for x in b if x['type'] == 'tw-transcript'][0]; c = 0
    for s in tr['content']['segments']:
        for pat, rep in R:
            s['text'], k = re.subn(pat, rep, s['text']); c += k
    open(f, 'w', encoding='utf-8').write(t[:m.start(1)] + json.dumps(b, ensure_ascii=False, separators=(',', ':')) + t[m.end(1):])
    return c
if __name__ == '__main__':
    el, lo = p.rows()
    for n in (sys.argv[1:] or sorted(el + lo, key=int)): print('tw%s' % n, apply(n))
