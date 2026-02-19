# Deployment-Prozess erklärt: rsync vs Git-only

## Die zentrale Frage: Brauchen wir überhaupt rsync?

**Kurze Antwort:** Aktuell JA, für Code-Deployment. Aber es gibt Alternativen!

**Lange Antwort:** Es gibt zwei getrennte Prozesse - Content und Code. Lass uns beide genau anschauen.

---

## Der aktuelle Prozess

### 1. Content-Flow (über kirby-git-content Plugin)

```
┌─────────────────────────────────────────────────────────────┐
│                    CONTENT LIFECYCLE                         │
└─────────────────────────────────────────────────────────────┘

Production Server (Panel):
  │
  ├─ Editor erstellt/ändert Content
  │
  ▼
kirby-git-content Plugin:
  │
  ├─ Auto-commit in lokales Git
  │
  ├─ Auto-push zu GitHub
  │
  ▼
GitHub Repository:
  │
  ├─ Content ist jetzt versioniert
  │
  ▼
Lokale Entwicklung:
  │
  └─ git pull → Content verfügbar

```

**Wichtig:** Content wird NICHT via rsync deployed!
- Content entsteht auf Production (Panel)
- Plugin pusht zu GitHub
- Lokal: git pull holt Content

### 2. Code-Flow (über rsync)

```
┌─────────────────────────────────────────────────────────────┐
│                      CODE LIFECYCLE                          │
└─────────────────────────────────────────────────────────────┘

Lokale Entwicklung:
  │
  ├─ Code-Änderungen (PHP, TypeScript, CSS)
  │
  ├─ git commit && git push origin main
  │
  ▼
GitHub Actions:
  │
  ├─ Tests laufen (TypeScript, Prettier, Vitest)
  │
  ├─ Build: pnpm run build (Assets kompilieren)
  │
  ├─ Composer: Dependencies installieren
  │
  ▼
rsync Deployment:
  │
  ├─ rsync synchronisiert Files via SSH
  │
  ├─ EXKLUDIERT: content/, media/, accounts/
  │
  ├─ DEPLOYED: PHP, Templates, dist/, vendor/
  │
  ▼
Production Server:
  │
  └─ Neuer Code ist live!

```

**Wichtig:** Code wird via rsync deployed!
- Code-Änderungen lokal
- GitHub Actions baut & testet
- rsync überträgt zu Production
- Content bleibt unberührt (excluded)

---

## Warum rsync statt Git auf Production?

### Aktuelle rsync-Lösung

**Vorteile:**
✅ **Sauber getrennt:** Production .git bleibt unberührt
✅ **Keine Merge-Konflikte:** Production macht keine git pulls
✅ **Build-Artefakte:** Gebaute Assets (dist/) werden deployed
✅ **Selective Sync:** Nur was nötig ist wird übertragen
✅ **Atomic:** Deployment ist eine klare Aktion

**Nachteile:**
⚠️ **SSH-Key nötig:** GitHub Actions braucht SSH-Zugang
⚠️ **Mehr Komplexität:** rsync-Konfiguration mit Excludes
⚠️ **Einseitig:** Nur Push, kein automatisches Pull

### Wie rsync aktuell funktioniert:

1. **GitHub Actions baut lokal** (in CI)
   - `pnpm run build` → dist/ wird erstellt
   - `composer install --no-dev` → vendor/ optimiert

2. **rsync synchronisiert selektiv**
   ```bash
   rsync -avz --delete \
     --exclude 'content' \      # ← Content bleibt auf Server!
     --exclude 'media' \        # ← Media bleibt auf Server!
     --exclude 'site/accounts' \ # ← User-Accounts bleiben
     ./ user@server:/path/
   ```

3. **Production behält:**
   - Eigenes content/ (wird via Git-Plugin gepusht)
   - Eigenes media/ (Uploads)
   - Eigene accounts/
   - Eigenes .git/ (für Git-Plugin)

---

## Alternative: Git-only Deployment

Statt rsync könnte Production auch einfach `git pull` machen!

### Wie würde das aussehen?

```
┌─────────────────────────────────────────────────────────────┐
│              GIT-ONLY DEPLOYMENT FLOW                        │
└─────────────────────────────────────────────────────────────┘

Lokale Entwicklung:
  │
  ├─ Code-Änderungen
  │
  ├─ pnpm run build (Assets bauen)
  │
  ├─ git add dist/ (Built assets committen!)
  │
  ├─ git commit && git push
  │
  ▼
GitHub Repository:
  │
  └─ Code + Built Assets sind in Git
  
GitHub Webhook:
  │
  └─ Trigger an Production Server
  
Production Server:
  │
  ├─ git pull origin main
  │
  ├─ composer install --no-dev
  │
  └─ Fertig!

```

### Git-only Approach

**Vorteile:**
✅ **Einfacher:** Nur Git, kein rsync
✅ **Kein SSH-Key für rsync nötig:** GitHub Actions braucht nur Webhook
✅ **Echter GitOps:** Alles über Git
✅ **History:** Git-Log zeigt alle Deployments

**Nachteile:**
⚠️ **Built Assets in Git:** dist/ muss committed werden (Anti-Pattern)
⚠️ **Merge-Konflikte möglich:** Bei gleichzeitigen Änderungen
⚠️ **Komplexer bei Problemen:** git reset/revert statt rsync-Rollback
⚠️ **Webhook oder Cronjob nötig:** Automatisches Pull triggern

### Implementierung Git-only:

**Benötigt:**

1. **Webhook auf Production:**
   ```php
   // webhook.php auf Production
   if ($_GET['secret'] === getenv('WEBHOOK_SECRET')) {
       shell_exec('cd /path && git pull origin main');
       shell_exec('cd /path && composer install --no-dev');
   }
   ```

2. **GitHub Webhook konfigurieren:**
   - Repository → Settings → Webhooks
   - Payload URL: https://dein-server.de/webhook.php?secret=...
   - Trigger: Push events

3. **Built Assets committen:**
   ```bash
   # .gitignore ändern
   # /dist → dist/ rausnehmen
   
   git add dist/
   git commit -m "Add built assets"
   ```

---

## Vergleich: rsync vs Git-only

| Aspekt | rsync (aktuell) | Git-only |
|--------|-----------------|----------|
| **Komplexität** | ⚠️ Mittel (SSH + Excludes) | ✅ Niedrig (nur Git) |
| **Setup** | ⚠️ SSH-Keys, rsync-Config | ✅ Webhook, Git-Config |
| **Built Assets** | ✅ Nicht in Git | ⚠️ Müssen in Git |
| **Rollback** | ✅ Einfach (rsync vorherige Version) | ⚠️ Git revert |
| **Merge-Konflikte** | ✅ Keine | ⚠️ Möglich |
| **Trennung Code/Content** | ✅ Klar getrennt | ⚠️ Beide in Git |
| **GitHub Actions Last** | ⚠️ Baut + deployed | ✅ Nur Webhook |

---

## Empfehlung: Was ist besser?

### Bleib bei rsync wenn:

✅ Du Built Assets NICHT in Git haben willst
✅ Du klare Trennung Content/Code möchtest
✅ Du atomic Deployments bevorzugst
✅ Setup-Komplexität kein Problem ist

**→ Das ist der aktuelle (empfohlene) Ansatz!**

### Wechsel zu Git-only wenn:

✅ Du Git-only Workflow bevorzugst
✅ Dir Built Assets in Git egal sind
✅ Du Webhooks einrichten kannst/willst
✅ Simplizität wichtiger als Separation ist

---

## Hybrid-Ansatz: Das Beste aus beiden Welten

Es gibt auch einen Hybrid:

### Content: Git (automatisch)
- kirby-git-content Plugin pusht Content
- Production ist Git-Quelle für Content

### Code: rsync (CI-built)
- GitHub Actions baut Assets
- rsync deployed nur Code/Assets
- Excludes schützen Content

**Das ist die aktuelle Lösung und funktioniert gut!**

---

## Der komplette Flow (aktuell)

### Szenario 1: Editor erstellt Content

```
1. Panel auf Production
   ↓
2. Content erstellt/geändert
   ↓
3. kirby-git-content committed automatisch
   ↓
4. Plugin pushed zu GitHub
   ↓
5. Entwickler: git pull
   ↓
6. Content ist lokal verfügbar
```

**rsync involviert:** NEIN

### Szenario 2: Entwickler ändert Code

```
1. Lokal Code ändern (PHP/TS/CSS)
   ↓
2. git commit && git push
   ↓
3. GitHub Actions:
   - Tests laufen
   - Assets bauen (pnpm run build)
   - Composer installiert
   ↓
4. rsync deployed zu Production:
   - PHP-Files
   - Templates
   - dist/ (gebaute Assets)
   - vendor/
   ↓
5. Production hat neuen Code
```

**rsync involviert:** JA (für Code-Deployment)

### Szenario 3: Entwickler ändert Content lokal

```
1. Lokal content/ ändern
   ↓
2. git commit && git push
   ↓
3. GitHub Actions deployed via rsync
   ↓
4. ABER: content/ ist excluded!
   ↓
5. Lösung: Production muss git pull machen
   oder: Content nur via Panel ändern
```

**Problem:** Lokale Content-Änderungen werden nicht deployed!

**Lösung:** Content sollte primär via Panel geändert werden.

---

## FAQ

### Warum ist content/ in rsync excluded?

✅ **Damit Panel-Content nicht überschrieben wird!**

Wenn Editor Content im Panel erstellt und wir dann rsync laufen lassen, würde der neue Content gelöscht werden (--delete Flag).

### Kann ich Content lokal ändern?

⚠️ **Ja, aber kompliziert:**

1. Lokal content/ ändern
2. git push
3. Production muss git pull machen
4. Oder: Nur im Panel ändern

**Empfehlung:** Content via Panel, Code via Git/rsync.

### Was passiert bei git pull auf Production?

Wenn Production `git pull` macht:
- Content Updates werden geholt
- Aber: Kann Merge-Konflikte geben
- Wenn Panel zur gleichen Zeit ändert

**Deshalb:** Panel ist Quelle für Content (pusht zu Git).

### Muss ich .git/ auf Production haben?

**JA!** Für kirby-git-content Plugin.

Das Plugin braucht `.git/` um:
- Content zu committen
- Zu GitHub zu pushen

### Was wird überhaupt mit rsync deployed?

**Deployed:**
- ✅ PHP-Files (index.php, site/templates/, etc.)
- ✅ JavaScript/TypeScript (dist/)
- ✅ CSS (dist/)
- ✅ Kirby Core (kirby/)
- ✅ Vendor (vendor/)
- ✅ Config (site/config/)

**NICHT deployed (excluded):**
- ❌ content/ (Panel-Content)
- ❌ media/ (Uploads)
- ❌ site/accounts/ (User-Daten)
- ❌ site/cache/ (Temporary)
- ❌ site/sessions/ (Temporary)
- ❌ .git/ (bleibt auf Production)

---

## Zusammenfassung

### Brauchen wir rsync?

**Ja, für Code-Deployment!**

- Content läuft über Git (kirby-git-content)
- Code läuft über rsync (GitHub Actions)
- Beide Prozesse sind getrennt
- Das ist gut so!

### Alternativen?

**Git-only ist möglich, aber:**
- Built Assets müssen in Git
- Webhook/Cronjob für auto-pull
- Komplexer bei Merge-Konflikten
- Weniger clean Separation

### Empfehlung:

✅ **Bleib beim aktuellen Hybrid-Ansatz:**
- Content: Git (automatisch via Plugin)
- Code: rsync (CI-built, tested)
- Klare Trennung
- Bewährte Lösung

---

## Nächste Schritte

Wenn du bei rsync bleiben möchtest:
→ **Folge TODO.md** - Setup ist schon perfekt!

Wenn du zu Git-only wechseln möchtest:
→ **Sag Bescheid** - Ich kann die Dokumentation/Config anpassen!

Bei Fragen:
→ **Dieses Dokument** erklärt alles!

---

## Weiterführende Dokumentation

- 📖 [TODO.md](TODO.md) - Setup-Anleitung
- 📖 [DEPLOYMENT.md](DEPLOYMENT.md) - Technische Details
- 📖 [PLUGIN_COMPARISON.md](PLUGIN_COMPARISON.md) - Plugin-Vergleich
- 📖 [DEPLOYMENT_QUICKREF.md](DEPLOYMENT_QUICKREF.md) - Schnell-Referenz

---

**Fazit:** rsync ist für Code-Deployment nötig und sinnvoll. Content läuft separat über Git. Das ist der beste Ansatz! 🎯
