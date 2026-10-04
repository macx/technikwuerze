"""Participant helpers for episode metadata.
usage: meta.py list | stub First Last slug TW<N> | add N H|G Name... | remove N H|G Name | guestmod Name
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
    body = f"""Title: {first} {last}\n\n----\n\nFirst-name: {first}\n\n----\n\nLast-name: {last}\n\n----\n\nProfession:\n\n----\n\nLead: ⚠ TODO: Profil vervollständigen (automatisch angelegt durch transkript-metadaten, Quelle: {source})\n\n----\n\nDescription:\n\n----\n\nExternal-profiles:\n\n----\n\nLinked-user:\n\n----\n\nProfile-image:\n\n----\n\nParticipant-role: guest\n\n----\n\nAdditional-roles:\n\n----\n\nGender-identities:\n\n----\n\nSelf-described-gender:\n\n----\n\nPronouns:\n\n----\n\nUuid: {u}\n"""
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
    else:
        print(__doc__ or 'usage: meta.py list | stub First Last slug TW<N> | add N H|G Name... | remove N H|G Name | guestmod Name')
