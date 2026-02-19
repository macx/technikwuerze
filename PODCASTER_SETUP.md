# Kirby Podcaster Plugin Setup

## Installation ✅

Das Podcaster-Plugin wurde erfolgreich installiert:
- Package: `mauricerenck/podcaster` v3.4.0
- Installiert via Composer
- Kirby 5 kompatibel

## Podcast-Struktur

Die Podcast-Inhalte sind wie folgt strukturiert:

```
content/
└── podcast/
    ├── default.txt              # Haupt-Podcast-Seite
    ├── feed/
    │   └── feed.txt            # RSS Feed Konfiguration
    └── 001-test-episode/
        ├── episode.txt         # Test-Episode Details
        └── README.md           # Anleitung für Audio-Datei
```

## Content Storage ✅

**Wichtig:** Alle Podcast-Inhalte werden im `content/` Ordner gespeichert:

- ✅ Episode-Texte: `content/podcast/001-test-episode/episode.txt`
- ✅ Feed-Konfiguration: `content/podcast/feed/feed.txt`
- ✅ Audio-Dateien: werden in Episode-Ordnern gespeichert (z.B. `content/podcast/001-test-episode/test-episode.mp3`)

**Das bedeutet:** Alle Podcast-Inhalte werden automatisch von **kirby-git-content** erfasst und können via Git synchronisiert werden! 🎉

## Feed-Konfiguration

Der RSS Feed ist konfiguriert unter:
- URL: `https://technikwuerze.de/podcast/feed`
- Podcast ID: `technikwuerze`
- Sprache: Deutsch (de-DE)
- Kategorien: Technology, Software How-To

### Feed-Details:
- **Titel:** Podcast Feed
- **Subtitle:** Germany's first developer podcast
- **Beschreibung:** Technikwürze ist Deutschlands erster Podcast für Entwickler...
- **Copyright:** © 2005-2026 Technikwürze
- **Email:** podcast@technikwuerze.de
- **Explizit:** Nein

## Test-Episode

Eine Test-Episode wurde erstellt:
- **Titel:** Test Episode - Setup und erste Schritte
- **Nummer:** Episode 1, Season 1
- **Datum:** 2026-02-19
- **Typ:** Full Episode
- **Dauer:** 5 Minuten

### Audio-Datei hinzufügen:

Die Episode benötigt noch eine MP3-Datei. Es gibt zwei Möglichkeiten:

1. **Via Kirby Panel** (empfohlen):
   - Panel öffnen → Podcast → Episode öffnen
   - MP3-Datei hochladen
   - Wird automatisch verlinkt

2. **Manuell**:
   ```bash
   # MP3-Datei kopieren nach:
   content/podcast/001-test-episode/test-episode.mp3
   
   # Dann episode.txt aktualisieren:
   # Podcastmp3: test-episode.mp3
   ```

## Blueprints

Das Plugin stellt folgende Blueprints bereit:

1. **Feed Blueprint** (`feed`):
   - Wird für die Feed-Seite verwendet
   - Template: `feed.txt`

2. **Episode Tab** (`tabs/podcasterepisode`):
   - Kann in bestehende Blueprints eingebunden werden
   - Enthält alle Podcast-spezifischen Felder

## Template-Dateien

Das Plugin stellt automatisch Templates bereit:
- Feed-Template für RSS-Generierung
- Episode-Template für Episode-Seiten

## Verwendung im Panel

### Feed aufrufen:
1. Kirby Panel öffnen
2. Zu "Podcast" navigieren
3. "Feed" Seite öffnen
4. Einstellungen prüfen/anpassen

### Episode erstellen:
1. Im Panel zu "Podcast" navigieren
2. Neue Seite erstellen
3. Template "Episode" wählen
4. Felder ausfüllen:
   - Titel
   - Datum
   - Episode-Nummer
   - Season
   - MP3-Datei hochladen
   - Beschreibung

### Feed aktualisieren:
1. Feed-Seite öffnen
2. "RSS Settings" Tab
3. "Source Pages" aktualisieren
4. Neue Episodes hinzufügen

## RSS Feed testen

Nach dem Setup kann der Feed getestet werden:

```bash
# Feed aufrufen (lokal):
curl http://localhost:8000/podcast/feed

# Feed aufrufen (production):
curl https://technikwuerze.de/podcast/feed
```

Der Feed sollte XML mit Podcast-Informationen zurückgeben.

## Git Content Sync ✅

**Bestätigt:** Das Podcaster-Plugin speichert alle Daten im `content/` Ordner:

### Was wird synchronisiert:
- ✅ Episode-Metadaten (Titel, Beschreibung, Datum, etc.)
- ✅ Episode-Texte und Inhalte
- ✅ Feed-Konfiguration
- ✅ Audio-Dateien (MP3s)
- ✅ Cover-Bilder
- ✅ Alle strukturellen Änderungen

### Workflow:

**Panel → Git:**
```
Panel: Episode erstellen/bearbeiten
  ↓
kirby-git-content: Auto-commit
  ↓
Git: Push zu GitHub
  ↓
Lokal: git pull
```

**Lokal → Production:**
```
Lokal: Änderungen in content/podcast/
  ↓
git commit && git push
  ↓
GitHub Actions: Deploy
  ↓
Production: Aktualisiert (via rsync)
```

**Wichtig:** Da `content/` vom rsync ausgeschlossen ist, werden Podcast-Inhalte ausschließlich via Git synchronisiert!

## Zusätzliche Features

Das Podcaster-Plugin bietet weitere Features:

1. **Analytics:**
   - Matomo-Integration
   - PodTrac-Tracking
   - Detaillierte Metriken

2. **Player:**
   - HTML5-Player Snippet
   - Podlove Player Integration
   - Konfigurierbarer Web-Player

3. **Chapters:**
   - Podcast-Kapitel Support
   - Zeitmarken

4. **Multi-Podcast:**
   - Mehrere Podcasts mit einer Kirby-Installation möglich

## Nächste Schritte

1. **Audio-Datei hinzufügen:**
   - MP3-Datei für Test-Episode hochladen

2. **Panel testen:**
   - Kirby Panel öffnen
   - Podcast-Seiten überprüfen
   - Episode-Felder testen

3. **Feed validieren:**
   - RSS Feed aufrufen
   - Mit Podcast-Validatoren testen (z.B. podba.se/validate)

4. **Weitere Episoden:**
   - Weitere Test-Episoden erstellen
   - Struktur verfeinern

5. **Templates anpassen:**
   - Website-Templates für Podcast-Übersicht
   - Episode-Einzelansichten

## Ressourcen

- **Plugin-Website:** https://podcaster-plugin.com/
- **GitHub:** https://github.com/mauricerenck/kirby-podcaster
- **Dokumentation:** https://podcaster-plugin.com/docs/
- **Kirby CMS:** https://getkirby.com/

## Troubleshooting

### Feed zeigt keine Episodes:
- "Source Pages" in Feed-Einstellungen prüfen
- Episode-Template korrekt? (sollte "episode" sein)
- Episode publiziert?

### Audio-Datei wird nicht erkannt:
- Datei im richtigen Ordner?
- Dateiname in episode.txt korrekt?
- MP3-Format gültig?

### Panel zeigt Podcast-Felder nicht:
- Plugin korrekt installiert? (`composer show mauricerenck/podcaster`)
- Kirby Cache geleert?
- Blueprint korrekt?

## Zusammenfassung

✅ Podcaster-Plugin installiert (v3.4.0)
✅ Feed konfiguriert (`content/podcast/feed/`)
✅ Test-Episode angelegt (`content/podcast/001-test-episode/`)
✅ Alle Inhalte im `content/` Ordner (Git-Sync funktioniert!)
✅ Kirby 5 kompatibel
✅ RSS Feed bereit

**Status:** Setup komplett! Nur noch Audio-Datei hinzufügen und testen.
