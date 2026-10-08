"""Participant helpers for episode metadata.
usage: meta.py list | stub First Last slug TW<N> | add N H|G Name... | remove N H|G Name | guestmod Name
       meta.py catalog | topics N | settopics N main|general "Topic" ["Topic" ...]
H = Team & Gastmoderation (Podcasterhosts), G = Gäste (Podcasterguests). Stubs are created unlisted (no numeric folder prefix)."""
import glob, re, os, random, string
from pathlib import Path
ROOT = str(Path(__file__).resolve().parents[2]) + '/'
def parts():
    d = {}
    for f in glob.glob(ROOT + 'content/3_teilnehmende/*/participant.txt'):
        t = open(f, encoding='utf-8').read()
        d[re.search(r'^Title:[ \t]*(.*)$', t, re.M).group(1).strip()] = re.search(r'^Uuid:[ \t]*(\S+)', t, re.M).group(1)
    return d
def new_uuid():
    used = set(parts().values())
    while True:
        u = ''.join(random.choice(string.ascii_lowercase + string.digits) for _ in range(16))
        if u not in used: return u
def stub(first, last, slug, source):
    d = ROOT + 'content/3_teilnehmende/' + slug; assert not os.path.exists(d), d
    os.makedirs(d); u = new_uuid()
    body = f"""Title: {first} {last}\n\n----\n\nFirst-name: {first}\n\n----\n\nLast-name: {last}\n\n----\n\nProfession:\n\n----\n\nDescription: ⚠ TODO: Profil vervollständigen (automatisch angelegt durch transkript-metadaten, Quelle: {source})\n\n----\n\nExternal-profiles:\n\n----\n\nLinked-user:\n\n----\n\nProfile-image:\n\n----\n\nParticipant-role: guest\n\n----\n\nAdditional-roles:\n\n----\n\nGender-identities:\n\n----\n\nSelf-described-gender:\n\n----\n\nPronouns:\n\n----\n\nUuid: {u}\n"""
    open(d + '/participant.txt', 'w', encoding='utf-8').write(body); print('stub', slug, u)
def add(n, field, names):
    P = parts()
    f = glob.glob(ROOT + f'content/2_mediathek/*/*tw{n}-*/episode.txt'); assert len(f) == 1
    f = f[0]; t = open(f, encoding='utf-8').read()
    k = 'Podcasterhosts' if field == 'H' else 'Podcasterguests'
    m = re.search(r'^' + k + r':.*?\n\n----', t, re.M | re.S | re.I); ids = re.findall(r'page://(\w+)', m.group(0))
    for nm in names:
        if P[nm] not in ids: ids.append(P[nm])
    new = f'{k}: - page://{ids[0]}\n\n----' if len(ids) == 1 else f'{k}:\n\n' + '\n'.join(f'- page://{i}' for i in ids) + '\n\n----'
    open(f, 'w', encoding='utf-8').write(t[:m.start()] + new + t[m.end():]); print(n, k, ids)


def guestmod(name):
    """Add the 'Gastmoderation' extra role (guest_moderation) to a guest's participant page."""
    for f in glob.glob(ROOT + 'content/3_teilnehmende/*/participant.txt'):
        t = open(f, encoding='utf-8').read()
        if re.search(r'^Title:[ \t]*' + re.escape(name) + r'[ \t]*$', t, re.M):
            assert 'Participant-role: guest' in t, 'only guests can be Gastmoderation'
            if 'Guest-roles' in t:
                print('already has Guest-roles', f); return
            t2 = re.sub(r'(Additional-roles:[ \t]*\n\n----\n)', r'\1\nGuest-roles: guest_moderation\n\n----\n', t, count=1)
            assert t2 != t, 'Additional-roles field not found'
            open(f, 'w', encoding='utf-8').write(t2); print('guest_moderation set for', name); return
    raise SystemExit('participant not found: ' + name)


def remove(n, field, name):
    """Remove a participant from an episode field (only on explicit instruction from David)."""
    P = parts(); u = P[name]
    f = glob.glob(ROOT + f'content/2_mediathek/*/*tw{n}-*/episode.txt')[0]; t = open(f, encoding='utf-8').read()
    k = 'Podcasterhosts' if field == 'H' else 'Podcasterguests'
    m = re.search(r'^' + k + r':.*?\n\n----', t, re.M | re.S | re.I); ids = [i for i in re.findall(r'page://(\w+)', m.group(0)) if i != u]
    new = f'{k}: \n\n----' if not ids else (f'{k}: - page://{ids[0]}\n\n----' if len(ids) == 1 else f'{k}:\n\n' + '\n'.join(f'- page://{i}' for i in ids) + '\n\n----')
    open(f, 'w', encoding='utf-8').write(t[:m.start()] + new + t[m.end():]); print(n, k, ids)


def catalog():
    """General topics catalog maintained in the Panel (Site > Settings > Topic catalog)."""
    t = open(ROOT + 'content/site.txt', encoding='utf-8').read()
    m = re.search(r'^General-topics-catalog:[ \t]*(.*)$', t, re.M)
    return [x.strip() for x in m.group(1).split(',') if x.strip()] if m else []


def episode_file(n):
    f = glob.glob(ROOT + f'content/2_mediathek/*/*tw{n}-*/episode.txt'); assert len(f) == 1, f'episode tw{n} not found'
    return f[0]


def read_field(t, key):
    m = re.search(r'^' + key + r':[ \t]*(.*?)\n\n----', t, re.M | re.S | re.I)
    return [x.strip() for x in m.group(1).split(',') if x.strip()] if m else []


def topics(n):
    t = open(episode_file(n), encoding='utf-8').read()
    print('main topic     (Topics):         ', ', '.join(read_field(t, 'Topics')) or '-')
    print('general topics (General-topics): ', ', '.join(read_field(t, 'General-topics')) or '-')
    print('catalog:', ', '.join(catalog()))


def settopics(n, kind, values):
    """Set the main topic(s) (kind 'main', field Topics, max 3) or general topics (kind 'general', field
    General-topics, only values from the catalog). Replaces the field value; creates the field before Uuid if missing."""
    key = {'main': 'Topics', 'general': 'General-topics'}[kind]
    if kind == 'general':
        allowed = {c.lower(): c for c in catalog()}
        unknown = [v for v in values if v.lower() not in allowed]
        assert not unknown, f'not in the topic catalog: {unknown} (catalog: {sorted(allowed.values())})'
        values = [allowed[v.lower()] for v in values]
    else:
        assert 1 <= len(values) <= 3, 'main topic: 1 to 3 values'
    f = episode_file(n); t = open(f, encoding='utf-8').read()
    line = f'{key}: ' + ', '.join(values)
    m = re.search(r'^' + key + r':.*?(?=\n\n----)', t, re.M | re.S | re.I)
    if m:
        t = t[:m.start()] + line + t[m.end():]
    else:
        u = re.search(r'^Uuid:', t, re.M); assert u, 'Uuid field not found'
        t = t[:u.start()] + line + '\n\n----\n\n' + t[u.start():]
    open(f, 'w', encoding='utf-8').write(t); print(n, line)


if __name__ == '__main__':
    import sys
    cmd = sys.argv[1] if len(sys.argv) > 1 else ''
    if cmd == 'list':
        for k, v in sorted(parts().items()): print(v, k)
    elif cmd == 'stub':      # meta.py stub "First" "Last" slug TW<N>
        stub(*sys.argv[2:6])
    elif cmd == 'add':       # meta.py add <N> H|G "Name" ["Name" ...]
        add(sys.argv[2], sys.argv[3], sys.argv[4:])
    elif cmd == 'remove':    # meta.py remove <N> H|G "Name"
        remove(sys.argv[2], sys.argv[3], sys.argv[4])
    elif cmd == 'guestmod':  # meta.py guestmod "Name"
        guestmod(sys.argv[2])
    elif cmd == 'catalog':   # meta.py catalog
        print('\n'.join(catalog()))
    elif cmd == 'topics':    # meta.py topics <N>
        topics(sys.argv[2])
    elif cmd == 'settopics': # meta.py settopics <N> main|general "Topic" ["Topic" ...]
        settopics(sys.argv[2], sys.argv[3], sys.argv[4:])
    else:
        print(__doc__ or 'usage: meta.py list | stub ... | add ... | remove ... | guestmod Name | catalog | topics N | settopics N main|general Topic...')
