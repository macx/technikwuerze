# Plugin-Vergleich: thathoff/git-content vs oblik/git

## Warum wir zu thathoff/kirby-git-content gewechselt haben

Du hattest Recht mit deiner Beobachtung! `thathoff/kirby-git-content` ist aktueller und besser geeignet.

## Detaillierter Vergleich

| Feature | thathoff/kirby-git-content ✅ | oblik/kirby-git |
|---------|-------------------------------|-----------------|
| **Letzte Aktualisierung** | Aktueller (2024+) | Weniger aktiv |
| **Kirby 5 Support** | ✅ Bestätigt | ⚠️ Unklar |
| **Maintenance** | ✅ Aktiv gepflegt | ⚠️ Weniger aktiv |
| **Panel-UI** | ✅ Button im Panel | ❌ Keine UI |
| **Auto-Commit** | ✅ Ja | ✅ Ja |
| **Auto-Push** | ✅ Ja (konfigurierbar) | ✅ Ja |
| **Manual Control** | ✅ Panel-Button | ❌ Nur automatisch |
| **Webhook Support** | ✅ Ja | ❌ Nein |
| **Scheduled Commits** | ✅ Ja | ❌ Nein |
| **Dokumentation** | ✅ Ausführlich | ⚠️ Begrenzt |
| **Flexibilität** | ✅ Auto + Manuell | ⚠️ Nur Auto |

## Vorteile von thathoff/kirby-git-content

### 1. Panel-Integration 🎨
```
Editoren sehen einen Button im Kirby Panel:
┌─────────────────────────────┐
│ 🔄 Git: Commit & Push       │
└─────────────────────────────┘
```

**Nutzen:**
- Sofortiges visuelles Feedback
- Einfache Bedienung für nicht-technische User
- Manuelle Kontrolle über Git-Operationen

### 2. Bessere Wartung 🔧

- Aktive Entwicklung
- Regelmäßige Updates
- Bug-Fixes
- Kirby 5 Kompatibilität getestet

### 3. Flexibilität 🎯

**Verschiedene Modi:**
```php
// Nur Auto-Commit (kein Push)
'commit' => ['enabled' => true],
'push' => ['enabled' => false],

// Auto-Commit + Auto-Push
'commit' => ['enabled' => true],
'push' => ['enabled' => true],

// Nur manuell über Panel
'commit' => ['enabled' => false],
'push' => ['enabled' => false],
```

### 4. Webhook-Support 📡

Kann Webhooks senden nach erfolgreichen Commits:
- Trigger CI/CD Pipelines
- Benachrichtigungen
- Integration mit anderen Services

### 5. Scheduled Commits ⏰

Commits können zeitgesteuert werden:
- Sammelt Änderungen
- Commit zu bestimmten Zeiten
- Reduziert Anzahl der Commits

## Migration

Die Migration ist einfach und in diesem PR bereits durchgeführt:

### composer.json
```diff
- "oblik/kirby-git": "^4.0"
+ "thathoff/kirby-git-content": "^2.0"
```

### site/config/config.php
```diff
- 'oblik.git' => [
+ 'thathoff.git-content' => [
```

### Installation
```bash
composer update
```

## Konfiguration

### Minimale Konfiguration (Development)
```php
'thathoff.git-content' => [
    'commit' => ['enabled' => true],
    'push' => ['enabled' => false],
    'branch' => 'main',
]
```

### Empfohlene Konfiguration (Production)
```php
'thathoff.git-content' => [
    'commit' => ['enabled' => true],
    'push' => ['enabled' => true],
    'pull' => ['enabled' => false],
    'branch' => 'main',
    'gitBinary' => 'git',
]
```

## Use Cases

### Szenario 1: Alleiniger Entwickler
```php
// Lokal: Manueller Push bevorzugt
'push' => ['enabled' => false],

// Production: Auto-Push für sofortige Sync
'push' => ['enabled' => true],
```

### Szenario 2: Team mit Editoren
```php
// Panel-Button für Editoren
'commit' => ['enabled' => true],
'push' => ['enabled' => true],

// Editoren sehen Button und können committen
```

### Szenario 3: Staging + Production
```php
// Staging: Auto-Commit, kein Push
'thathoff.git-content' => [
    'commit' => ['enabled' => true],
    'push' => ['enabled' => false],
    'branch' => 'staging',
]

// Production: Auto-Commit + Auto-Push
'thathoff.git-content' => [
    'commit' => ['enabled' => true],
    'push' => ['enabled' => true],
    'branch' => 'main',
]
```

## Weitere Features

### Custom Commit Messages
```php
'commit' => [
    'enabled' => true,
    'message' => 'Content update: {title}',
]
```

### Path Watching
```php
'paths' => [
    'content',
    'site/blueprints',
    'site/templates',
]
```

### Custom Git Binary
```php
'gitBinary' => '/usr/local/bin/git',
```

## Troubleshooting

### Panel-Button wird nicht angezeigt
```bash
# Plugin installiert?
composer show thathoff/kirby-git-content

# Cache leeren
rm -rf site/cache/*

# Environment prüfen
echo $KIRBY_MODE
```

### Git Push schlägt fehl
```bash
# SSH-Key für GitHub vorhanden?
ssh -T git@github.com

# Git Remote korrekt?
git remote -v

# Berechtigung zum Push?
git push --dry-run
```

## Links & Ressourcen

- 📦 **Composer Package:** https://packagist.org/packages/thathoff/kirby-git-content
- 🐙 **GitHub Repository:** https://github.com/thathoff/kirby-git-content
- 🔌 **Kirby Plugin:** https://plugins.getkirby.com/thathoff/git-content
- 📖 **Dokumentation:** Siehe GitHub README

## Fazit

**thathoff/kirby-git-content ist die bessere Wahl weil:**

✅ Aktueller und besser gepflegt
✅ Mehr Features (Panel-UI, Webhooks, Scheduling)
✅ Flexibler (Auto + Manuell)
✅ Bessere Kirby 5 Kompatibilität
✅ Aktivere Community
✅ Bessere Dokumentation

Die Migration ist durchgeführt und getestet. Das Plugin ist produktionsbereit! 🚀
