# Agent Notes: Migration

## Zweck

Dieser Ordner ist ein lokaler Arbeitsbereich für Datenmigration und QA, nicht für regulären Runtime-Code.

## Regeln

1. Keine Änderungen aus `migration/data`, `migration/scripts` oder `migration/reports` in den regulären Produktbetrieb einbauen, ohne explizite Rückfrage.
2. Migrationsskripte müssen mit Pfaden relativ zum Projekt-Root funktionieren (Aufruf z. B. `php migration/scripts/...`).
3. `content/.db/*.sqlite` und Audio-Binaries nicht per Git verteilen; nur per `rsync`.
4. Nach jeder Script-Änderung kurze Validierung durchführen (`php -l`, Stichprobe auf Output-Dateien).

## Aktueller Kontext

- Episoden-Audio ist zentral auf `content/audio/` umgestellt.
- `Podcasteraudio`-Referenzen wurden auf eine zentrale Test-UUID vereinheitlicht.
- Kommentar-Import + Threading läuft über die Skripte in diesem Ordner.
