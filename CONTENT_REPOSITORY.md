# Content Repository Setup Guide

## Overview

Following the [kirby-git-content documentation](https://github.com/thathoff/kirby-git-content#create-a-new-git-repository-for-your-content), the `content/` directory should be a **separate Git repository**.

## Why Separate Repository?

### Benefits:

✅ **Clean separation**: Code changes don't mix with content changes
✅ **Better history**: Content commits are isolated
✅ **Flexible deployment**: Content syncs via Git, code via rsync
✅ **Team workflow**: Content editors don't need main repo access

### Architecture:

```
Main Repository (macx/technikwuerze)
├── Code, templates, assets
└── .gitignore includes: /content

Content Repository (content/)
├── Separate Git repository
├── Managed by kirby-git-content plugin
└── Panel changes → auto-commit → auto-push
```

## Setup Instructions

### 1. Initialize Content Repository

```bash
cd content/

# For Kirby 5: Add _changes folder to .gitignore
echo "_changes" >> .gitignore

# Initialize Git repository
git init

# Make initial commit
git add .
git commit -m "Initial content commit"

# Add remote (create empty repo on GitHub first)
git remote add origin git@github.com:macx/technikwuerze-content.git

# Push to GitHub
git branch -M main
git push -u origin main
```

### 2. Remove content/ from Main Repository

```bash
# Back to main repository
cd ..

# Remove content/ from Git tracking
git rm --cached -r content

# Add to .gitignore (already done in this setup)
# /content is now in .gitignore

# Commit the change
git add -A
git commit -m "Move content/ to separate repository"
git push origin main
```

### 3. Configure kirby-git-content Plugin

The plugin is already configured in `site/config/config.php`:

```php
// Development
'thathoff.git-content' => [
    'commit' => ['enabled' => true],
    'push' => ['enabled' => false],  // Manual push locally
    'branch' => 'main',
]

// Production (config.production.php)
'thathoff.git-content' => [
    'commit' => ['enabled' => true],
    'push' => ['enabled' => true],   // Auto-push on production
    'branch' => 'main',
]
```

### 4. Setup on Production Server

```bash
# SSH to production server
ssh user@server

# Navigate to deployment directory
cd /path/to/technikwuerze

# Initialize content repository
cd content/
git init
git remote add origin git@github.com:macx/technikwuerze-content.git
git pull origin main

# Configure Git user (for plugin commits)
git config user.email "panel@technikwuerze.de"
git config user.name "Kirby Panel"

# Ensure SSH key for GitHub push
# (Already covered in TODO.md Phase 4)
```

## Daily Workflow

### Content Created in Panel (Production)

```
1. Editor creates/edits page in Panel
   ↓
2. kirby-git-content commits automatically
   ↓
3. Plugin pushes to GitHub (if enabled)
   ↓
4. Content is versioned in separate repo
```

### Getting Content Locally

```bash
# Navigate to content directory
cd content/

# Pull latest from production
git pull origin main

# Content is now synced locally
```

### Editing Content Locally (Not Recommended)

If you must edit content locally:

```bash
cd content/

# Make changes
edit some-page/page.txt

# Commit
git add .
git commit -m "Update content"

# Push to GitHub
git push origin main

# On production, plugin will pull (if pull enabled)
# OR: Manually pull on production
```

**Recommendation:** Edit content via Panel, not locally!

## Current Status

✅ **rsync excludes content/**: Verified in `.github/workflows/deploy.yml`

```yaml
--exclude 'content'
```

✅ **Main .gitignore excludes content/**: Added `/content` to .gitignore

✅ **Content/.gitignore created**: Contains `_changes` for Kirby 5

⚠️ **Content Git repository**: Needs to be initialized (see Step 1 above)

## Important Notes

### Git Setup

The content/ directory will have its **own** `.git/` folder:

```
technikwuerze/
├── .git/           # Main repository
├── content/
│   ├── .git/       # Content repository (separate!)
│   ├── .gitignore  # Content-specific ignores
│   └── ...         # Content files
└── ...
```

### Not a Submodule

This is NOT a Git submodule. It's simply a separate Git repository inside a parent directory that's gitignored.

### VS Code Git Handling

VS Code is configured to ignore the content repository:

```json
// .vscode/settings.json
"git.ignoredRepositories": [
  "${workspaceFolder}/content"
]
```

This prevents confusion between the two repositories.

## Troubleshooting

### Panel shows "Git not initialized"

```bash
cd content/
git init
git add .
git commit -m "Initial commit"
```

### Plugin not pushing

1. Check `site/config/config.production.php`
2. Verify `push.enabled` is `true`
3. Check SSH key setup for GitHub
4. Test: `ssh -T git@github.com`

### Merge conflicts

If panel and local changes conflict:

```bash
cd content/
git status
# Resolve conflicts manually
git add .
git commit -m "Resolve conflicts"
git push origin main
```

**Best practice:** Avoid local content edits!

## Alternative: Cron-based Push

Instead of auto-push after every save, use a cron job:

```bash
# /etc/cron.d/kirby-content-push
*/15 * * * * www-data cd /path/to/technikwuerze/content && git push origin main
```

This batches commits and makes panel saves faster.

Or setup webhook: `yourdomain.com/git-content/push`

## Summary

The content repository setup follows kirby-git-content best practices:

1. ✅ Separate Git repository in content/
2. ✅ .gitignore for Kirby 5 (\_changes)
3. ✅ Excluded from main repo
4. ✅ rsync doesn't deploy it
5. ✅ Plugin manages Git operations
6. ✅ VS Code configured to handle both repos

**Next Step:** Initialize the content Git repository as shown in Step 1!
