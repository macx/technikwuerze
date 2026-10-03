#!/usr/bin/env python3
"""Builds docs/transkripte/arbeitsliste.md: all listed episodes without transcript, by downloads.

Usage (from project root): python3 scripts/build-transcript-worklist.py
"""
import glob, os, re, sqlite3, datetime

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
C = os.path.join(ROOT, 'content')
MAC_ROOT = '/Users/macx/Projects/macx/technikwuerze'
BASE = 'http://127.0.0.1:8000'

def fields(path):
    data, key, buf = {}, None, []
    for part in re.split(r'\n----\n', open(path, encoding='utf-8').read()):
        m = re.match(r'\s*([A-Za-z0-9_-]+):\s?(.*)', part, re.S)
        if m:
            data[m.group(1).lower()] = m.group(2).strip()
    return data

def key(slug):
    s = re.sub(r'^\d+_', '', slug.lower())
    m = re.match(r'tw(\d+)(?:-|$)', s)
    return f'tw{int(m.group(1))}' if m else s

# participants by uuid
people = {}
for f in glob.glob(f'{C}/3_teilnehmende/*/participant.txt'):
    d = fields(f)
    name = (d.get('first-name', '') + ' ' + d.get('last-name', '')).strip() or d.get('title', '')
    people[d.get('uuid', '')] = name

# audio by uuid
audio = {}
for f in glob.glob(f'{C}/audio/*.mp3.txt'):
    d = fields(f)
    audio[d.get('uuid', '')] = (os.path.basename(f)[:-4], d.get('duration', ''))

downloads = {}
db = f'{C}/.db/podcaster.sqlite'
if os.path.isfile(db):
    for slug, n in sqlite3.connect(db).execute('SELECT episode_slug, SUM(downloads) FROM episodes GROUP BY episode_slug'):
        downloads[key(slug)] = downloads.get(key(slug), 0) + int(n or 0)

rows, done = [], 0
for f in glob.glob(f'{C}/2_mediathek/*/*/episode.txt'):
    folder = os.path.basename(os.path.dirname(f))
    season_dir = os.path.basename(os.path.dirname(os.path.dirname(f)))
    if not re.match(r'^\d{12}_', folder) or '_drafts' in f:
        continue  # only listed episodes
    d = fields(f)
    if '"type":"tw-transcript"' in open(f, encoding='utf-8').read():
        done += 1
        continue
    uid = re.sub(r'^\d+_', '', folder)
    season_slug = re.sub(r'^\d+_', '', season_dir)
    uuids = lambda v: re.findall(r'page://([a-z0-9]+)', v or '')
    hosts = [people.get(u, '?') for u in uuids(d.get('podcasterhosts'))]
    guests = [people.get(u, '?') for u in uuids(d.get('podcasterguests'))]
    au = re.search(r'file://([a-z0-9]+)', d.get('podcasteraudio', ''))
    afile, dur = audio.get(au.group(1), ('', '')) if au else ('', '')
    rows.append({
        'total': int(d.get('podcasterepisodetotal') or 0),
        'p': d.get('podcasterseason', ''), 'e': d.get('podcasterepisode', ''),
        'title': d.get('title', ''), 'date': d.get('date', '')[:10],
        'dur': dur, 'speakers': hosts + guests, 'guests': guests,
        'audio': f'{MAC_ROOT}/content/audio/{afile}' if afile else '— fehlt —',
        'panel': f'{BASE}/panel/pages/mediathek+{season_slug}+{uid}',
        'dl': downloads.get(key(uid), 0),
    })
    notes = []
    if not afile:
        notes.append('⚠ keine Audiodatei verknüpft')
    if re.match(r'^00:0[0-4]:', dur or ''):
        notes.append('⚠ Laufzeit < 5 min – Audio prüfen')
    if not hosts + guests:
        notes.append('Sprecher nicht hinterlegt')
    rows[-1]['note'] = '; '.join(notes)

rows.sort(key=lambda r: (-r['dl'], r['total']))
today = datetime.date.today().isoformat()
out = [f"""# Transkript-Arbeitsliste

Stand: {today} · {len(rows)} Folgen ohne Transkript · {done} mit Transkript · sortiert nach Downloads (meistgehörte zuerst).
Neu erzeugen: `python3 scripts/build-transcript-worklist.py` und danach `pnpm exec prettier --write docs/transkripte/arbeitsliste.md` (Häkchen gehen verloren – Erledigtes fällt automatisch heraus).

## Briefing für den Cowork-Agenten (macOS, Steuerung von Chrome)

**Ziel:** Für jede Folge unten ein Transkript mit Sprecherkennung erzeugen und über das Panel-Plugin „Transkripte“ in die Folge importieren. Arbeite die Liste von oben nach unten ab, **immer nur eine Folge vollständig**, dann die nächste.

**Voraussetzungen (David stellt sicher):** Dev-Server läuft auf `{BASE}`, David ist in Chrome im Kirby-Panel und beim Transkriptionsdienst angemeldet. Du gibst **nie** selbst Passwörter oder Zahlungsdaten ein und bestätigst keine kostenpflichtigen Aktionen – frag David.

**Ablauf je Folge:**
1. **Transkribieren:** Im Transkriptionsdienst (Standard: ElevenLabs Speech-to-Text) die Audiodatei aus der Spalte „Audio“ hochladen. Sprache: Deutsch. Sprechererkennung (Diarization) an, Anzahl Sprecher = Anzahl Namen in „Sprecher“.
2. **Exportieren:** Als **JSON** (bevorzugt, enthält Sprecher und Zeitstempel) oder TXT mit Zeitstempeln herunterladen.
3. **Importieren:** Im Panel unter `{BASE}/panel/tw-transcript/importer` die Datei hochladen, in der Vorschau die Sprecher-Platzhalter („Speaker 1/2…“) den Namen aus „Sprecher“ zuordnen, Ziel-Folge über den Link „Panel“ auswählen, importieren.
4. **Prüfen:** Die Folge im Panel öffnen (Link „Panel“): Transkript-Block vorhanden, Sprechernamen korrekt, erste und letzte Zeitmarke plausibel zur Laufzeit.
5. **Abhaken:** In dieser Datei die Checkbox der Folge setzen `[x]` und ggf. eine Notiz ergänzen.

**Stopp-Regeln:** Bei Fehlern im Import, unklarer Sprecherzuordnung, fehlender Audiodatei oder Kosten-/Kontingent-Hinweisen des Dienstes: anhalten, Notiz in die Zeile, David fragen. Keine Folge veröffentlichen, umbenennen oder andere Felder ändern.

**Vorab markierte Auffälligkeiten (Spalte „Notiz“):** „⚠ keine Audiodatei“ und „⚠ Laufzeit < 5 min“ → nicht transkribieren, David melden (vermutlich defekte oder gekürzte MP3/Metadaten). „Sprecher nicht hinterlegt“ → Stimmen im Transkript neutral als „Sprecher 1/2…“ belassen und Namen in der Notiz vorschlagen, wenn sie sich aus dem Gespräch ergeben.

**Hinweise:** Sprecher sind die Moderatoren und Gäste laut Folgen-Metadaten; bei alten Folgen können weitere Stimmen (Einspieler, News-Sprecher) vorkommen – diese als „Einspieler“ benennen und in der Notiz vermerken. Die Rollen in den Metadaten sind teilweise ungenau (siehe Designprüfung FD-3).

## Liste
"""]
out.append('| ✓ | # | P·E | Titel | Datum | Laufzeit | Downloads | Sprecher | Audio | Panel | Notiz |')
out.append('|---|---|---|---|---|---|---|---|---|---|---|')
for r in rows:
    t = r['title'].replace('|', '\\|')
    sp = ', '.join(r['speakers']) or '—'
    out.append(f"| [ ] | {r['total']} | P{r['p']}·E{r['e']} | {t} | {r['date']} | {r['dur'] or '—'} | {r['dl']:,}".replace(',', '.') + f" | {sp} | `{r['audio']}` | [Panel]({r['panel']}) | {r['note']} |")
path = os.path.join(ROOT, 'docs/transkripte/arbeitsliste.md')
open(path, 'w', encoding='utf-8').write('\n'.join(out) + '\n')
print(path, len(rows), 'rows,', done, 'done')
