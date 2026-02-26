# Migration Workspace

Dieser Ordner enthält einmalige/temporäre Import-Artefakte für die Technikwürze-Migration.

## Struktur

- `migration/data/`  
  Quell-Daten (WordPress XML, CSV, gemappte JSONs).
- `migration/scripts/`  
  Import-/Migrationsskripte.
- `migration/reports/`  
  QA-Ausgaben (z. B. Kommentar-Threading-Reports).

## Wichtige Entscheidungen

- Audio liegt zentral in `content/audio/` (nicht mehr pro Episode-Ordner).
- Episoden referenzieren Audio per `Podcasteraudio: - file://<uuid>`.
- Laufzeit-DBs liegen in `content/.db/` und werden **nicht** per Git versioniert.

## Skripte (Kurzüberblick)

- `import_episodes_from_csv.php`  
  Importiert Episoden/Teilnehmende aus CSV; nutzt zentrale Audio-UUID.
- `import_wp_episode_content.php`  
  Übernimmt Datum, Summary und Content aus WordPress-XML in Episoden.
- `normalize_episode_markdown.php`  
  Normalisiert importierte Inhalte auf Markdown.
- `import_wp_comments_to_komments_sqlite.php`  
  Importiert WordPress-Kommentare in `content/.db/komments.sqlite`.
- `map_legacy_reply_mentions.php`  
  Heuristische Reply-Zuordnung (`@Name`, Anreden), begrenzt auf 2 Ebenen.
- `generate_comment_threading_qa.php`  
  Erstellt QA-CSV zu offenen Threading-Kandidaten in `migration/reports/`.
- `extract_wp_comments.php`  
  Extrahiert Kommentare aus XML in JSON zur Analyse.

## Hinweise

- Diese Daten/Artefakte sind absichtlich lokal und nicht Teil des regulären Deployments.
- Binaries/DB-Dateien per `rsync` synchronisieren, nicht per Git.
