# TODO: Deployment Setup Anleitung

Diese Anleitung führt dich Schritt für Schritt durch die Einrichtung des automatischen Deployments mit rsync und Git Content-Sync.

## Voraussetzungen

- [ ] GitHub Account mit Admin-Rechten für das Repository
- [ ] SSH-Zugang zum Production Server
- [ ] Git auf dem Production Server installiert
- [ ] Composer auf dem Production Server installiert
- [ ] PHP 8.2+ auf dem Production Server
- [ ] Webserver (Apache/Nginx) auf dem Production Server konfiguriert

---

## Phase 1: Lokale Vorbereitung

### 1.1 Repository aktualisieren

```bash
# In deinem lokalen Repository
git pull origin main
```

### 1.2 Dependencies installieren

```bash
# PHP Dependencies (inkl. neues kirby-git-content Plugin)
composer update

# Node Dependencies
pnpm install
```

### 1.3 Tests lokal durchführen

```bash
# Alle Tests ausführen
pnpm run test

# Build testen
pnpm run build
```

✅ **Checkpoint:** Alle Tests sollten grün sein!

---

## Phase 2: SSH-Keys für Deployment erstellen

### 2.1 SSH-Key-Pair für GitHub Actions generieren

```bash
# Auf deinem lokalen Rechner
ssh-keygen -t ed25519 -C "github-deploy@technikwuerze" -f ~/.ssh/technikwuerze_deploy

# Private Key anzeigen (für GitHub Secret)
cat ~/.ssh/technikwuerze_deploy

# Public Key anzeigen (für Server)
cat ~/.ssh/technikwuerze_deploy.pub
```

### 2.2 Public Key auf Production Server hinzufügen

```bash
# SSH auf Production Server
ssh dein-user@dein-server.de

# Authorized keys bearbeiten
nano ~/.ssh/authorized_keys

# Public Key aus Schritt 2.1 hier einfügen
# Datei speichern (Ctrl+O, Enter, Ctrl+X)

# Berechtigungen prüfen
chmod 600 ~/.ssh/authorized_keys
chmod 700 ~/.ssh
```

### 2.3 SSH-Verbindung testen

```bash
# Auf deinem lokalen Rechner
ssh -i ~/.ssh/technikwuerze_deploy dein-user@dein-server.de

# Sollte ohne Passwort-Eingabe funktionieren!
```

✅ **Checkpoint:** SSH-Verbindung mit Key funktioniert!

---

## Phase 3: GitHub Secrets konfigurieren

Gehe zu: **GitHub Repository → Settings → Secrets and variables → Actions → New repository secret**

### 3.1 DEPLOY_SSH_KEY anlegen

- **Name:** `DEPLOY_SSH_KEY`
- **Value:** Kompletter Inhalt von `~/.ssh/technikwuerze_deploy` (Private Key)
  ```bash
  cat ~/.ssh/technikwuerze_deploy | pbcopy  # macOS
  # oder
  cat ~/.ssh/technikwuerze_deploy           # Linux - manuell kopieren
  ```

### 3.2 DEPLOY_HOST anlegen

- **Name:** `DEPLOY_HOST`
- **Value:** Deine Server-Domain oder IP
  ```
  Beispiel: technikwuerze.de
  oder: 123.45.67.89
  ```

### 3.3 DEPLOY_USER anlegen

- **Name:** `DEPLOY_USER`
- **Value:** SSH-Username auf dem Server
  ```
  Beispiel: webuser
  oder: technikwuerze
  ```

### 3.4 DEPLOY_PATH anlegen

- **Name:** `DEPLOY_PATH`
- **Value:** Absoluter Pfad zum Deployment-Verzeichnis
  ```
  Beispiel: /var/www/technikwuerze
  oder: /home/webuser/public_html
  ```

✅ **Checkpoint:** Alle 4 Secrets sind angelegt!

---

## Phase 4: Production Server vorbereiten

### 4.1 SSH auf Production Server

```bash
ssh dein-user@dein-server.de
```

### 4.2 Git Repository initialisieren

```bash
# Zum Deployment-Verzeichnis wechseln
cd /var/www/technikwuerze  # Dein DEPLOY_PATH

# Git initialisieren (falls noch nicht vorhanden)
git init

# Remote hinzufügen
git remote add origin git@github.com:macx/technikwuerze.git

# Oder falls bereits vorhanden, URL prüfen:
git remote -v
```

### 4.3 Git User konfigurieren

```bash
# Auf dem Production Server
cd /var/www/technikwuerze  # Dein DEPLOY_PATH

git config user.email "panel@technikwuerze.de"
git config user.name "Kirby Panel"
```

### 4.4 SSH-Key für GitHub erstellen (für Git Push vom Server)

```bash
# Auf dem Production Server
ssh-keygen -t ed25519 -C "server@technikwuerze.de" -f ~/.ssh/github_deploy

# Public Key anzeigen
cat ~/.ssh/github_deploy.pub
```

**Wichtig:** Kopiere diesen Public Key!

### 4.5 Public Key zu GitHub hinzufügen

1. Gehe zu: **GitHub → Settings → SSH and GPG keys → New SSH key**
2. **Title:** `Production Server - Technikwuerze`
3. **Key:** Public Key aus Schritt 4.4 einfügen
4. **Save**

### 4.6 SSH-Verbindung zu GitHub testen

```bash
# Auf dem Production Server
ssh -T git@github.com

# Sollte antworten: "Hi macx/technikwuerze! You've successfully authenticated..."
```

### 4.7 Environment auf Production setzen

```bash
# Auf dem Production Server
cd /var/www/technikwuerze  # Dein DEPLOY_PATH

# .env Datei erstellen
nano .env
```

Inhalt der `.env` Datei:

```env
KIRBY_MODE=production
```

Speichern: `Ctrl+O`, `Enter`, `Ctrl+X`

### 4.8 Composer Dependencies installieren

```bash
# Auf dem Production Server
cd /var/www/technikwuerze  # Dein DEPLOY_PATH

# Production Dependencies installieren
composer install --no-dev --optimize-autoloader --no-interaction
```

### 4.9 Verzeichnis-Berechtigungen setzen

```bash
# Auf dem Production Server
cd /var/www/technikwuerze  # Dein DEPLOY_PATH

# Web-Server User ermitteln (meist www-data oder nginx)
ps aux | grep -E 'apache|nginx' | head -1

# Besitzer setzen (www-data als Beispiel)
sudo chown -R www-data:www-data .

# Beschreibbare Verzeichnisse
sudo chmod -R 775 content media site/cache site/sessions

# Git-Verzeichnis sollte auch vom Web-Server beschreibbar sein
sudo chown -R www-data:www-data .git
```

✅ **Checkpoint:** Server ist vorbereitet!

---

## Phase 5: Ersten Deployment ausführen

### 5.1 Branch erstellen und pushen

```bash
# Auf deinem lokalen Rechner
git add .
git commit -m "Setup deployment configuration"
git push origin main
```

### 5.2 GitHub Actions überwachen

1. Gehe zu: **GitHub Repository → Actions Tab**
2. Warte auf den "Deploy" Workflow
3. Klicke auf den laufenden Workflow
4. Beobachte die einzelnen Steps

**Erwartete Steps:**

- ✅ Checkout code
- ✅ Setup Node.js & pnpm
- ✅ Run tests
- ✅ Build production assets
- ✅ Setup PHP & Composer
- ✅ Deploy to server (rsync)
- ✅ Clear Kirby cache

### 5.3 Deployment verifizieren

```bash
# SSH auf Production Server
ssh dein-user@dein-server.de

# Deployment-Verzeichnis prüfen
cd /var/www/technikwuerze  # Dein DEPLOY_PATH
ls -la

# Sollte enthalten:
# - dist/ (gebaute Assets)
# - kirby/
# - site/
# - vendor/
# - index.php
# etc.
```

### 5.4 Website im Browser testen

Öffne: `https://dein-server.de` (oder deine Domain)

**Erwartetes Ergebnis:** Website lädt mit Styling ✅

✅ **Checkpoint:** Erstes Deployment erfolgreich!

---

## Phase 6: Git Content-Sync testen

### 6.1 Kirby Panel aufrufen

```
https://dein-server.de/panel
```

### 6.2 Im Panel einloggen oder Account erstellen

Falls noch kein Account vorhanden:

```bash
# SSH auf Production Server
cd /var/www/technikwuerze  # Dein DEPLOY_PATH
php kirby/bin/kirby create:user admin@technikwuerze.de --role admin
```

### 6.3 Testinhalt erstellen

1. Im Panel: **Pages → New Page**
2. Titel: "Test Deployment"
3. Content hinzufügen
4. **Save**

### 6.4 Git-Änderungen prüfen (auf Server)

```bash
# SSH auf Production Server
cd /var/www/technikwuerze  # Dein DEPLOY_PATH

# Git Status prüfen
git status

# Sollte neue/geänderte Dateien in content/ zeigen
```

### 6.5 Panel-Button "Git Commit & Push" verwenden

1. Im Kirby Panel: Links in der Sidebar
2. Suche nach "Git" Icon oder Button
3. Klicke auf "Commit & Push"
4. Bestätige die Aktion

**Oder manuell committen (falls Button nicht sichtbar):**

```bash
# SSH auf Production Server
cd /var/www/technikwuerze  # Dein DEPLOY_PATH

git add content/
git commit -m "Content update via Panel"
git push origin main
```

### 6.6 Lokal pullen und verifizieren

```bash
# Auf deinem lokalen Rechner
cd /pfad/zu/technikwuerze
git pull origin main

# Prüfen ob content/ aktualisiert wurde
ls -la content/
```

✅ **Checkpoint:** Content-Sync funktioniert!

---

## Phase 7: Automatisierung verifizieren

### 7.1 Test: Code-Änderung deployen

```bash
# Lokal eine Datei ändern
echo "/* Test comment */" >> src/index.ts

# Committen und pushen
git add src/index.ts
git commit -m "Test: Deployment verification"
git push origin main
```

**Erwartung:** GitHub Action startet automatisch

### 7.2 Test: Content-Änderung vom Panel

1. Im Panel eine Page bearbeiten
2. Git Commit & Push Button nutzen
3. Lokal: `git pull origin main`

**Erwartung:** Änderungen sind lokal sichtbar

### 7.3 Test: Lokal Content ändern

```bash
# Lokal Content-Datei bearbeiten
nano content/home/home.txt

# Ändern und speichern
git add content/
git commit -m "Content update from local"
git push origin main
```

**Erwartung:**

- GitHub Action deployt
- Änderungen sind auf dem Server sichtbar

✅ **Checkpoint:** Alle Workflows funktionieren!

---

## Troubleshooting Checkliste

Falls etwas nicht funktioniert:

### rsync schlägt fehl:

- [ ] Alle 4 GitHub Secrets korrekt gesetzt?
- [ ] SSH-Key (Private) komplett kopiert? (inkl. BEGIN/END)
- [ ] Public Key auf Server in authorized_keys?
- [ ] Server-Pfad (DEPLOY_PATH) existiert?
- [ ] Server-User hat Schreibrechte?

### Git Push vom Server funktioniert nicht:

- [ ] SSH-Key für GitHub erstellt?
- [ ] Public Key zu GitHub hinzugefügt?
- [ ] `ssh -T git@github.com` auf Server erfolgreich?
- [ ] Git Remote korrekt konfiguriert?

### Kirby Panel zeigt Git-Button nicht:

- [ ] Plugin installiert? (`composer show thathoff/kirby-git-content`)
- [ ] Config korrekt? (`site/config/config.production.php`)
- [ ] KIRBY_MODE=production gesetzt?
- [ ] Cache geleert? (`rm -rf site/cache/*`)

### Website zeigt 500 Error:

- [ ] Composer Dependencies installiert?
- [ ] Datei-Berechtigungen korrekt?
- [ ] PHP-Version >= 8.2?
- [ ] Error-Log prüfen: `tail -f /var/log/apache2/error.log`

### Tests schlagen fehl:

- [ ] Lokal: `pnpm install` ausgeführt?
- [ ] Lokal: `pnpm run test` erfolgreich?
- [ ] TypeScript-Fehler? (`pnpm run type-check`)
- [ ] Format-Fehler? (`pnpm run format`)

---

## Zusammenfassung der Reihenfolge

1. ✅ **Lokal:** Tests durchführen, sicherstellen alles funktioniert
2. ✅ **Lokal:** SSH-Keys für Deployment generieren
3. ✅ **Server:** Public Key in authorized_keys hinzufügen
4. ✅ **GitHub:** 4 Secrets konfigurieren (SSH_KEY, HOST, USER, PATH)
5. ✅ **Server:** Git Repository initialisieren
6. ✅ **Server:** Git User konfigurieren
7. ✅ **Server:** SSH-Key für GitHub erstellen
8. ✅ **GitHub:** Server Public Key als Deploy Key hinzufügen
9. ✅ **Server:** Environment setzen (KIRBY_MODE=production)
10. ✅ **Server:** Composer Dependencies installieren
11. ✅ **Server:** Verzeichnis-Berechtigungen setzen
12. ✅ **Lokal:** Code pushen → Erstes Deployment via GitHub Actions
13. ✅ **Verify:** Website im Browser testen
14. ✅ **Verify:** Panel aufrufen, Content erstellen
15. ✅ **Verify:** Git Commit & Push vom Panel testen
16. ✅ **Verify:** Content lokal pullen
17. ✅ **Fertig!** 🎉

---

## Nächste Schritte

Nach erfolgreichem Setup:

- [ ] Backup-Strategie implementieren
- [ ] SSL-Zertifikat einrichten (Let's Encrypt)
- [ ] Monitoring aufsetzen (Uptime, Errors)
- [ ] Regelmäßige Updates planen (`composer update`, `pnpm update`)
- [ ] Dokumentation für Team-Mitglieder erstellen

---

## Hilfreiche Befehle

```bash
# Deployment manuell triggern (ohne Code-Änderung)
# GitHub → Actions → Deploy Workflow → Run workflow

# Server-Logs live ansehen
tail -f /var/log/apache2/error.log  # Apache
tail -f /var/log/nginx/error.log    # Nginx

# Kirby Cache löschen
rm -rf site/cache/*

# Git Status auf Server prüfen
cd /var/www/technikwuerze && git status

# Composer Dependencies aktualisieren
composer update --no-dev

# Node Dependencies aktualisieren
pnpm update
```

---

## Support & Dokumentation

- 📖 Komplette Anleitung: [DEPLOYMENT.md](DEPLOYMENT.md)
- 🚀 Schnellreferenz: [DEPLOYMENT_QUICKREF.md](DEPLOYMENT_QUICKREF.md)
- 📝 README: [README.md](README.md)
- 🔌 Plugin-Doku: https://github.com/thathoff/kirby-git-content

Bei Problemen:

1. Workflow-Logs in GitHub Actions prüfen
2. Server Error-Logs prüfen
3. Diese TODO.md Schritt für Schritt durchgehen
4. GitHub Issues durchsuchen

**Viel Erfolg mit dem Deployment! 🚀**
