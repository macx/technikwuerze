# Designprüfung 25.08.2026 – Umsetzungsliste

Quelle: `2026-08-25-website-designpruefung.pdf` (Stefans Agent). Status je Befund, Reihenfolge nach Abschnitt 11 des Berichts.

Legende: `[x]` erledigt · `[~]` war bei Prüfung am 01.10. bereits behoben, bitte bestätigen · `[ ]` offen · `[-]` bewusst verworfen

## 1 – Kritisch

- [x] **K-1** Social-Links zeigen auf tote Domain → 8 echte Profil-URLs, alle aktiv bestätigt (Discord/LinkedIn entfernt)
- [x] **K-2** Name vereinheitlicht → Site-Tab „Anbieter“ + Block `provider` (Impressum, Datenschutz), Adress-Block mit Quelle „Anbieter“ (Kontakt)
- [x] **K-3** Relaunch-Status → Block „Letzte Folge“ schaltet ab 60 Tagen auf „Zuletzt erschienen“ + Datum + Hinweis; Frequenzzusage „Ab Phase 4 …“ (Meta, Footer) ; RSS-Feed-Beschreibung ebenso angepasst
- [x] **K-4** Gravatar-Anfragen → Komments nutzt `avatar.service: initials`, 0 Gravatar-Requests auf TW188

## 2 – Startseite

- [x] **ST-1** Wortmarke ist `<h1>`, Byline `<p>`; `<title>` Startseite aus neuem Feld `seoTitle` („Technikwürze · Podcast für Medienschaffende“)
- [x] **ST-2** Bug behoben: „Beliebteste“ zeigte wegen Slug-Präfix die neuesten Folgen → `lib/episode-stats.php` normalisiert Slugs, fasst Downloads je Folge zusammen (auch Top-10-Kachel); Karten zeigen Downloads, „Neu entdecken“ mit Satz „Zufällig aus 188 Folgen seit 2005“; keine Dubletten auf der Startseite; totes Feld `popularEpisodes` entfernt
- [x] **ST-2b** Stats-DB bereinigt: 188 Legacy-Slugs auf Live-Slugs umgeschrieben, 0 Präfix-Slugs übrig, Summe unverändert; in Produktion gepusht
- [x] **ST-3** Statistik: „Veröffentlichte Folgen“, Prozent deutsch („11,2 %“, NBSP), Monatszahl ersetzt durch neuen Werttyp „Jahre seit erster Folge (live)“ → „Jahre Technikwürze“, Top-10-Label „davon Top-10-Folgen“
- [ ] **ST-4** Mitwirkende nur als Personen-Icons – schematische Darstellung ist Absicht; offen: Gäste als Klickgrund gehen verloren → mit Stefan besprechen (Vorschlag: Avatare + max. 2 Namen, Gäste vorrangig)
- [x] **ST-5** Teaser-Titel per `->kti()` statt `->kt()` (kein `<p>` mehr im `<h2>`)
- [ ] **ST-6** Icon-only-Links in Kopf/Fuß (9 Plattformen, 8 Social) – offen, mit Stefan besprechen (Optionen: A Namen auf Touch per `@media (hover: none)`, B Reduktion auf 3–4 Favoriten + „Überall sonst hören“)

## 3 – Mediathek

- [ ] **MK-1** 188 Folgen ohne Filter/Sortierung – offen, Designprojekt mit Stefan (Befund: Format- und Themen-Taxonomie existiert nicht, Jahr/Phase/Personen/Länge vorhanden; Card Sorting vorab empfohlen)
- [x] **MK-2** Live-Meta „Zeitraum · Anzahl Folgen“ unter der Phasen-Überschrift (Mediathek + Phasenseite, `twSeasonMeta()`) ; Leads P1–P3 ohne getippte Zahlen (P1 nannte fälschlich 108 – TW107/108 gehören zu Phase 2)
- [x] **MK-3** Phase 4 sichtbar → Season-Toggle „In der Mediathek ankündigen“ + Hinweis „Die erste Folge ist in Vorbereitung.“
- [-] **MK-4** „Die letzten 3 Folgen“ bleibt bewusst (prominenter Einstieg, Liste zeigt weiterhin alle Folgen der Phase); verwaistes Feld `Content:` aus mediathek.txt entfernt
- [x] **MK-5** Befund unzutreffend („Inhalte“ durchsucht bereits Seiten, Folgen, Teilnehmende ohne Kommentare) → Optionen nach Reichweite benannt: „Alles außer Kommentare“ (Standard), „Alles inkl. Kommentare“, „Nur Folgen/Teilnehmende/Kommentare“; Dialog übernimmt aktuelle Kategorie
- [x] **Begriffe** Frontend-Begriff „Folge“ festgelegt (Kurzform P · E · # bleibt), Such-Badge „Episode“ → „Folge“, in AGENTS.md § 5 dokumentiert
- [ ] **MK-6** Kein Bild in der Mediathek – offen, mit Stefan besprechen (Befund: nur 2 generische Cover für 189 Folgen; Optionen: A Cover pro Phase automatisch, B Bilder aus Shownotes, C bis Phase 4 zurückstellen)

## 4 – Folgendetailseite

- [ ] **FD-1** Keine Kapitelmarken, kein Transkript – Technik vorhanden (`kirby-tw-transcript`); Transkripte für alle Folgen mit Audio erstellt und importiert (Stand 2026-10-04, Skills `transkript-import` und `transkript-metadaten`, Skripte in `scripts/transcripts/`, Wortdaten-Archiv in `content/.transcripts/`); Kapitelmarken daraus ableiten bleibt offen
- [x] **FD-2** Breadcrumb (`snippet breadcrumb`, Folge/Phase/Teilnehmende, JSON-LD `BreadcrumbList`); Blätter-Buttons: sichtbarer Text Teil des zugänglichen Namens (WCAG 2.5.3, `sr-only` statt `aria-label`/`aria-hidden`); Regel „Auflösung der Kurzform = Episode“ in AGENTS.md
- [x] **FD-3** Rollenmodell „Team / Gast“ (statt Moderation/Host) in Panel und Frontend; Zusatzrollen Herausgeber · Moderation · Redaktion (Mehrfachauswahl); 7 Personen zu Team befördert, 6 echte Gäste in 18 Folgen aus „Team“ zu „Gäste“ verschoben, TW45 bereinigt (`migration/scripts/fd3_team_roles.py`)
- [x] **FD-4** Kommentare: erste 5 Threads sichtbar, Rest in `<details>` („Weitere 29 Kommentare anzeigen“), Sprungmarken öffnen den Bereich automatisch (`openCommentFromHash`)
- [x] **FD-5** Download-Link in der Meta-Zeile „Reguläre Folge · Download (MP3, 54 MB)“, über die Podcaster-Download-URL (zählt in der Statistik)

## 5 – Teilnehmende

- [x] **TN-1** Meta-Zeile unter dem Namen (`twParticipantMeta()` in `lib/participant-stats.php`, `.tw-participants-meta`): Team/Gastmoderation „N Folgen · Zeitraum“, Gäste mit einer Folge Hauptthema (Link zur Folge) + Jahr, sonst „N Folge(n) · Zeitraum“; Herausgeber ohne. Neues Folgenfeld „Themen“ (`topics`, Tags, max. 3, Hauptthema zuerst, nicht kleinteilig, Themenmix → generisch). Themen für die 26 Einzelgast-Folgen eingetragen; übrige Folgen bei Bedarf später
- [x] **TN-2** Seite gegliedert: h1 „Wer bei Technikwürze spricht“ (Feld Header), h2 „Eure Gastgeber“ · „Team“ · „Gäste“; Menü/Slug bleiben „Teilnehmende“
- [x] **TN-3** „Jagzent“ → „Jagszent“ überall (Name, Slug `daniel-jagszent`, 4 Shownotes), 301-Weiterleitung vom alten Slug in `base.php`
- [x] **TN-4** Handschriftlicher Absatz steht jetzt direkt vor der Gästeliste

## 6 – Kontakt

- [x] **KO-1** `type="email"` per Projekt-Override `site/snippets/blocks/form-field-email.php` (Plugin 0.9.5 = aktuellste Version, Bug auch auf main, kein Issue vorhanden)
- [x] **KO-2** Kontaktformular `autocomplete="on"` (`autoComplete => true` in `base.php`); Suchfelder unverändert
- [x] **KO-3** Datenschutzhinweis über dem Button wie bei Komments (`.form-privacy`, Text `tw.form.privacy`, Link `/datenschutz#kontaktformular`) via Override `site/snippets/form.php`
- [x] **KO-4** Honeypots mit `aria-hidden="true"`: Kontakt (`site/snippets/form.php`) und Kommentare (`kommentform.php`, Platzhalter „Leave empty“ entfernt)
- [x] **KO-5** Unter dem Formular: „Lieber per Mail? Schreib an …“ (Adresse aus Site → Anbieter) + „in der Regel innerhalb weniger Tage“
- [x] **KO-6** Paketlabel: Absender „Dir · Hoffentlich mit was Schönem drin“, DHL-Logo/„DHL Paket“/GoGreen neutralisiert, Grafik dekorativ (`aria-hidden`), Adresse zusätzlich als `<address>` mit „Adresse kopieren“-Button (`copy-button.ts`)

## 7/8 – Impressum & Datenschutz

- [x] **IM-1** Name veraltet (→ K-2)
- [x] **IM-2** Neues Feld „Stand“ (`revisedAt`) im Seiten-Blueprint, Ausgabe „Stand: Oktober 2026“ am Ende von Impressum und Datenschutz
- [x] **DS-1** Gravatar fehlt (→ K-4, Datenschutz am 27.08. ergänzt)
- [x] **DS-2** „§ 25 Abs. 2 Nr. 2 TTDSG“ → „TDDDG“ im Datenschutz
- [x] **DS-3** Podlove Web Player 5.13.0 jetzt selbst gehostet (`public/assets/podlove/web-player/`, vorher `cdn.podlove.org` – undokumentierte Drittanfrage!); `podcast-media.php` + Override `podcaster-podlove-player.php` setzen `base`/`reference.base`; Abschnitt „Podcast-Player“ im Datenschutz

## 9 – Barrierefreiheit

- [x] **A11Y-1** `--clr-text-light` hell 0,5 → 0,6 Deckkraft (5,6:1 auf `--clr-surface`)
- [x] **A11Y-2** Neues Token `--clr-primary-text` (hell L 24 %, 5,0:1 auf Gelb) für Links und kleinen Text in Markengrün: `a:any-link`, Fußnoten, Gästeliste, Teilnehmer-Statistik/-Profile, Folgennummer-Badge, Teaser-Nachwort; `--clr-primary` (Marke) unverändert
- [ ] **A11Y-2b** Folgennummer-Badge in der Mediathek steht auf `--clr-bg-dark`: dort nur 4,0:1 (bräuchte L ≤ 22 % oder hellere Badge-Fläche)
- [ ] **A11Y-2c** Empfehlungsblock: Tabellenköpfe `--clr-primary` mit `opacity: 0.65` ≈ 2,3:1 – später ansehen
- [x] **A11Y-3** Reduced-Motion-Guard für Markenanimation (weiter endlos, 10 s) + `scroll-behavior`
- [ ] **A11Y-3b** WCAG 2.2.2 (Pause, Stop, Hide): Endlos-Animation ohne Pause-Mechanismus – bewusst offen gelassen

## Nebenfunde (nicht im Bericht)

- [x] **NF-1** Verwaiste Ordner `2_mediathek/01-phase-1/` und `01-staffel-01/` (Kirby-Stubs aus dem Umbenennungs-Commit) gelöscht
- [x] **NF-2** TW103 (2007 auf Wunsch offline genommen) bleibt gelistet, Mediathek/Phasenseite zeigen „· nicht mehr verfügbar“ bei Folgen ohne Audio
- [x] **NF-3** Phase 1: `Podcasterepisode` bei TW101–TW106 korrigiert (vorher E102–E107, jetzt E = #); Phase 1 durchgehend E = #
- [x] **NF-4** Abgeschnittene Audiodateien TW147–TW156 durch vollständige Originale ersetzt; zusätzlich 56 aufgeblähte 128-kbps-Neukodierungen durch die kleineren Originale ersetzt (66 Dateien, −986 MB), Laufzeiten in den `.txt` korrigiert (`migration/scripts/replace_audio_with_originals.py`, Protokoll `migration/reports/audio-replacement.json`) – Upload per `sync:push:audio` durch David
- [x] **NF-5** TW153 „MODx total“: Gerrit van Aaken moderiert → von Gäste nach „Team & Gastmoderation“ verschoben; Gerrit bleibt Gast, mit Gastmoderation (→ NF-7)
- [x] **NF-6** 26 Folgen (TW121–TW183) verwiesen auf Teilnehmende per Pfad (`teilnehmende/…`, teils alter Slug `daniel-jagzent`) statt `page://`-UUID → Team/Gäste fehlten auf der Seite. Auf UUIDs umgestellt, Gäste aus „Team“ zu „Gäste“ verschoben (Dirk Ginader, Peter Kröner, Sylvia Egger, Tomas Caspers); Ergänzungen: TW154 + Peter Müller (Gast), TW155 + Marcel (Team), TW48 − Nadja (nicht im Audio). Skript `migration/scripts/nf6_participant_refs.py`. Sprecher-Spalte der Transkript-Arbeitsliste ergänzt
- [x] **NF-7** Gastmoderation (Variante B): neue Zusatzrolle `guest_roles: guest_moderation` für Gäste (Blueprint), Folgenfeld heißt „Team & Gastmoderation“; Folgenseite zeigt die erste Gruppe immer als „Moderation“ (Profil/Teilnehmende behalten Team/Gast); Profil „Gast · Gastmoderation“, Statistik „moderiert“. Gerrit van Aaken (TW153), Andreas Dantz (TW180–183) und Martin Labuschin (TW56, jetzt unter Moderation) und Marcel Schwarzenberger als Gast mit Gastmoderation. Offen: TW57 Metadaten manuell prüfen
- [x] **NF-8** „Ansger Hein“ → „Ansgar Hein“ (Name, Slug `ansgar-hein`, 301 vom alten Slug); TW16: Alex Wunschel und Ulf Beyschlag als Teilnehmende angelegt und als Gäste zugewiesen, „virtüll“ → „virtuell“
- [x] **NF-9** Migrationsfehler „ue“ → „ü“ zurückgedreht: 64 Ersetzungen in 41 geprüften Wortformen (u. a. „schaün“, „baün“, „genaür“, „individülle“, „Güsts“, „Manüle“ → „Manuela“), inkl. `_changes`-Entwürfe und JSON-escaped Blöcke; Skript `migration/scripts/nf9_fix_ue_umlaut.py`. Andere Tippfehler (z. B. „Technikwütze“, „Untersetützung“) nicht angefasst
- [x] **NF-10** TW121: Sylvia Egger als Gast ergänzt
- [ ] **NF-11** Transkript-Hörfehler beim Podcastnamen: „Technikwitze“ (~92×), „Technikwirtze“ (~72×), „Technikwirtse“ (~24×), „Technikwütze“ (~37×), „Technikwirt(z)“, „Technikwitz“, abgeschnittenes „Technikw“ u. ä. – vor allem in Transkript-Blöcken; Ersetzungsliste mit Kontextprüfung (Wortspiele wie „Technikwürzel“ bewusst lassen?), außerdem Einzel-Tippfehler wie „Untersetützung“
- [x] **NF-12** IndieConnector hat bei jedem Panel-Speichern Webmentions an alle verlinkten Seiten geschickt (auch vom Dev-Server; TW77/TW186/Startseite mit Fehlern) und `indieConnector.json` in die Seitenordner geschrieben. Jetzt: Senden nur live (`config.technikwuerze.de.php`), nur beim Statuswechsel/Veröffentlichen (`send.automatically` aus), nur Folgen (`allowedTemplates: episode`), Links aus `blocks` + `podcasterdescription`; 61 Outbox-Dateien gelöscht, `indieConnector.json` in `content/.gitignore`. Empfang von Webmentions unverändert
- [x] **NF-13** Seitengewicht: Webfonts auf Latein reduziert (`scripts/subset-fonts.py`, Originale in `assets-src/fonts/`): Case 371 → 172 KB, McQueen 87 → 63 KB, Supermarker 255 → 174 KB; Achsen/Features unverändert, Gewichtsbereich bleibt. Ungenutzte Italic-Schnitte von Case/McQueen aus `public/` nach `assets-src/fonts/` verschoben
- [x] **NF-14** Podlove `embed.js` (135 KB) lädt erst, wenn der Player in Sicht kommt: `kirby-tw-transcript` v0.10.0-beta.4 liest die URL aus `data-podlove-embed-src` (gesetzt in `podcast-media.php`), CDN-Fallback `cdn.podlove.org` entfernt (= Nebenbefund 9). Im Dev-Modus kurzer FOUT beobachtet (Vite liefert CSS per JS) – im Auge behalten, live prüfen
- [x] **NF-15** `blocks/podcast-stats.yml` war ungültiges YAML (`columns: label` mit verschachtelter Map darunter; Kirbys toleranter Parser hat es geschluckt). Jetzt `columns: label/integer_value/percent_value: true`; doppelte Option `msi-podcasts` entfernt; „Veröffentlichte Episoden“ → „Folgen“. Alle anderen Blueprints validieren
- [x] **NF-16** `kontakt.txt`: totes Feld `Text:` (Platzhalter „Text aus dem Panel“, kein Blueprint-Feld, nicht im Template) entfernt
