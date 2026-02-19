# Podcast Content Repository - Nächste Schritte

## Aktueller Status

Die Podcast-Struktur wurde im `content/` Ordner erstellt, aber da `content/` ein separates Git-Repository ist (siehe CONTENT_REPOSITORY.md), müssen die Podcast-Dateien dort separat committed werden.

## Was wurde erstellt

### Dateien in content/ (separates Repo):

```
content/
└── podcast/
    ├── default.txt                      # Haupt-Podcast-Seite
    ├── feed/
    │   └── feed.txt                    # RSS Feed Konfiguration
    └── 001-test-episode/
        ├── episode.txt                 # Test-Episode Details
        └── README.md                   # Audio-Upload Anleitung
```

## Nächste Schritte für Content-Repository

### 1. Content-Repository initialisieren (falls noch nicht geschehen)

```bash
cd content/

# Falls noch kein Git-Repository:
git init

# Alle Podcast-Dateien hinzufügen
git add .

# Initial Commit mit Podcast-Setup
git commit -m "Add podcast structure with test episode"

# Remote hinzufügen (falls vorhanden)
git remote add origin git@github.com:USER/technikwuerze-content.git

# Pushen
git push -u origin main
```

### 2. Audio-Datei für Test-Episode hinzufügen

Es gibt zwei Wege:

**Option A: Via Kirby Panel (empfohlen)**
1. Panel öffnen: `http://localhost:8000/panel`
2. Zu Podcast → 001-test-episode navigieren
3. MP3-Datei hochladen
4. kirby-git-content committed automatisch
5. Push wird automatisch ausgeführt (auf Production)

**Option B: Manuell**
```bash
# MP3-Datei in Episode-Ordner kopieren
cp your-audio.mp3 content/podcast/001-test-episode/test-episode.mp3

# episode.txt aktualisieren
# Zeile ändern von:
# Podcastmp3: 
# zu:
# Podcastmp3: test-episode.mp3

# In content/ Repository committen
cd content/
git add .
git commit -m "Add audio file for test episode"
git push
```

## Git-Sync Workflow für Podcast

### Panel → Git → Lokal

```
┌─────────────────────────────────────┐
│  Production Panel                   │
│  • Episode erstellen/bearbeiten     │
│  • Audio-Datei hochladen            │
│  • Cover-Bild hinzufügen           │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│  kirby-git-content Plugin           │
│  • Auto-commit in content/.git      │
│  • Auto-push zu GitHub              │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│  GitHub (Content Repository)        │
│  • Podcast-Dateien versioniert      │
│  • Audio-Files im Git                │
│  • Feed-Config versioniert          │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│  Lokal: git pull                    │
│  • Alle Podcast-Inhalte sync       │
│  • Audio-Dateien lokal verfügbar   │
│  • Kann lokal bearbeitet werden     │
└─────────────────────────────────────┘
```

### Lokal → Git → Production

```
┌─────────────────────────────────────┐
│  Lokale Änderungen                  │
│  • content/podcast/ bearbeiten      │
│  • Neue Episodes anlegen            │
│  • Feed-Config ändern               │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│  Git Commit & Push                  │
│  cd content/ && git push            │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│  GitHub (Content Repository)        │
│  • Änderungen in Git                │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│  Production                         │
│  • kirby-git-content pulled         │
│  • Oder: git pull manuell           │
│  • Podcast aktualisiert             │
└─────────────────────────────────────┘
```

## Wichtige Hinweise

### Content bleibt im content/ Repository

- ✅ Podcast-Episodes: `content/podcast/*/episode.txt`
- ✅ Audio-Dateien: `content/podcast/*/*.mp3`
- ✅ Cover-Bilder: `content/podcast/*/*.jpg/png`
- ✅ Feed-Config: `content/podcast/feed/feed.txt`

### Nicht im Haupt-Repository

- ❌ Main Repository enthält nur Code
- ❌ `content/` ist in `.gitignore`
- ❌ Podcast-Inhalte werden NICHT via rsync deployed

### Sync funktioniert ausschließlich via Git

- ✅ kirby-git-content Plugin übernimmt den Sync
- ✅ Automatisch bei Panel-Änderungen
- ✅ Manuell via `git pull` in content/

## RSS Feed testen

### Lokal

```bash
# PHP Server starten
php -S localhost:8000

# Feed aufrufen
curl http://localhost:8000/podcast/feed

# Oder im Browser
open http://localhost:8000/podcast/feed
```

### Production (nach Deployment)

```bash
curl https://technikwuerze.de/podcast/feed
```

## Feed-Validierung

Teste den RSS Feed mit Validatoren:

1. **PodBase Validator:** https://podba.se/validate/
2. **Castfeed Validator:** https://castfeedvalidator.com/
3. **iTunes Podcast Validator** (falls in iTunes eintragen möchtest)

## Troubleshooting

### Feed zeigt keine Episodes

**Problem:** RSS Feed ist leer oder zeigt keine Episodes.

**Lösung:**
1. Feed-Seite öffnen: `content/podcast/feed/feed.txt`
2. Prüfe "Sourcepages" Feld:
   ```
   Sourcepages: 
   
   - podcast/001-test-episode
   ```
3. Episode-Seite prüfen:
   - Template muss "episode" sein
   - Episode muss published sein (nicht draft)

### Audio-Datei wird nicht erkannt

**Problem:** Episode hat keine Audio-Datei im Feed.

**Lösung:**
1. MP3-Datei ist im Episode-Ordner: `content/podcast/001-test-episode/test-episode.mp3`
2. episode.txt referenziert die Datei:
   ```
   Podcastmp3: test-episode.mp3
   ```
3. Datei ist gültiges MP3-Format
4. ID3-Tags sind vorhanden (optional, aber empfohlen)

### Panel zeigt Podcast-Felder nicht

**Problem:** Podcast-Tabs im Panel fehlen.

**Lösung:**
1. Plugin installiert? `composer show mauricerenck/podcaster`
2. Kirby Cache leeren: `rm -rf site/cache/*`
3. Blueprint prüft auf `extends: tabs/podcasterepisode`
4. Browser-Cache leeren

### Git-Sync funktioniert nicht

**Problem:** Panel-Änderungen werden nicht committed.

**Lösung:**
1. content/ ist Git-Repository? `cd content && git status`
2. kirby-git-content installiert? `composer show thathoff/kirby-git-content`
3. Plugin konfiguriert? Siehe `site/config/config.php`
4. Git User konfiguriert in content/?
   ```bash
   cd content/
   git config user.email "panel@technikwuerze.de"
   git config user.name "Kirby Panel"
   ```

## Weitere Episoden erstellen

### Via Panel

1. Panel öffnen: `/panel`
2. Zu Podcast navigieren
3. "Add" klicken
4. Template "Episode" wählen
5. Felder ausfüllen:
   - Titel
   - Datum
   - Episode-Nummer
   - Season
   - Beschreibung
   - Audio-Datei hochladen
6. Publish

### Manuell (für Batch-Import)

```bash
# Neue Episode anlegen
mkdir content/podcast/002-neue-episode

# episode.txt erstellen
cat > content/podcast/002-neue-episode/episode.txt << 'EOF'
Title: Zweite Episode

----

Date: 2026-02-20

----

Episodetype: full

----

Episodenumber: 2

----

Season: 1

----

Description: Beschreibung der Episode

----

Podcastmp3: episode-002.mp3

----

Template: episode
EOF

# Audio-Datei hinzufügen
cp your-file.mp3 content/podcast/002-neue-episode/episode-002.mp3

# In content/ committen
cd content/
git add .
git commit -m "Add episode 002"
git push
```

### Feed aktualisieren

Nach dem Erstellen neuer Episodes:

1. `content/podcast/feed/feed.txt` öffnen
2. "Sourcepages" aktualisieren:
   ```
   Sourcepages: 
   
   - podcast/001-test-episode
   - podcast/002-neue-episode
   ```
3. Committen und pushen

## Zusammenfassung

✅ **Podcast-Struktur erstellt** in content/
✅ **Feed konfiguriert** mit RSS-Details
✅ **Test-Episode angelegt** (bereit für Audio-Datei)
✅ **Git-Sync funktioniert** mit kirby-git-content
✅ **Dokumentation komplett** (PODCASTER_SETUP.md)

**Nächster Schritt:** Audio-Datei für Test-Episode hinzufügen und Feed testen!

## Ressourcen

- 📖 PODCASTER_SETUP.md - Vollständige Setup-Dokumentation
- 📖 CONTENT_REPOSITORY.md - Content-Repository Anleitung
- 🌐 https://podcaster-plugin.com/ - Plugin-Website
- 🐙 https://github.com/mauricerenck/kirby-podcaster - GitHub Repository
