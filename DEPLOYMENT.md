# Deployment Guide

> **🚀 Quick Start:** Für eine Schritt-für-Schritt Anleitung zur Einrichtung, siehe [TODO.md](TODO.md)


This document explains how to set up and use the automated deployment process for the Technikwürze website.

## Overview

The deployment process consists of:

1. **Automated Testing**: Every push runs TypeScript checks, Prettier validation, and unit tests
2. **Automated Deployment**: Pushes to `main` branch automatically deploy to production server via SSH
3. **Content Sync**: Content changes made via Kirby Panel are automatically committed to Git

## GitHub Secrets Required

You need to configure the following secrets in your GitHub repository settings:

### Navigate to: Repository Settings → Secrets and variables → Actions → New repository secret

1. **DEPLOY_SSH_KEY**: Private SSH key for server access
   ```bash
   # Generate a new SSH key pair on your local machine:
   ssh-keygen -t ed25519 -C "github-actions@technikwuerze" -f ~/.ssh/deploy_key
   
   # Copy the private key content (entire file):
   cat ~/.ssh/deploy_key
   
   # Add the public key to your server's authorized_keys:
   ssh-copy-id -i ~/.ssh/deploy_key.pub user@your-server.com
   ```

2. **DEPLOY_HOST**: Your server hostname or IP address
   ```
   Example: technikwuerze.de or 123.45.67.89
   ```

3. **DEPLOY_USER**: SSH username on the server
   ```
   Example: webuser or technikwuerze
   ```

4. **DEPLOY_PATH**: Absolute path to deployment directory on server
   ```
   Example: /var/www/technikwuerze or /home/webuser/public_html
   ```

## Workflows

### Test Workflow (test.yml)

Runs on:
- Every push to `main` or `develop` branches
- Every pull request to `main`

Steps:
1. Checkout code
2. Setup Node.js and pnpm
3. Install dependencies
4. Run TypeScript type checking
5. Run Prettier format checking
6. Run Vitest unit tests
7. Build production assets

### Deploy Workflow (deploy.yml)

Runs on:
- Every push to `main` branch
- Manual trigger via GitHub Actions UI

Steps:
1. Run all tests
2. Build production assets
3. Install PHP/Composer dependencies (production mode)
4. Deploy to server via rsync over SSH
5. Clear Kirby cache on server

## Content Sync Strategy

### Kirby Git Content Plugin (thathoff/kirby-git-content)

The `thathoff/kirby-git-content` plugin is configured to automatically commit content changes.


#### On Production Server:
- ✅ Auto-commit enabled
- ✅ Auto-push to Git enabled (pushes to `main` branch)
- Content changes in panel → Git commit → Git push → Available everywhere

#### On Local/Development:
- ✅ Auto-commit enabled
- ❌ Auto-push disabled (manual git push)
- Pull latest content: `git pull origin main`

### Protected Content

The deployment process **excludes** the following directories to prevent overwriting server content:

- `content/` - Kirby content files (managed by Git plugin)
- `media/` - Uploaded media files
- `site/accounts/` - User accounts
- `site/cache/` - Cache files
- `site/sessions/` - Session data
- `storage/` - Any storage directory

### Content Workflow

#### Scenario 1: Content created on production server
1. Editor creates/modifies content via Kirby Panel on production
2. Kirby Git plugin automatically commits changes
3. Plugin automatically pushes to GitHub
4. Developer pulls changes locally: `git pull origin main`
5. Content is now available locally

#### Scenario 2: Content created locally
1. Developer modifies content locally (in `content/` directory)
2. Developer commits and pushes to GitHub
3. Production content repository pulls latest changes (`cd content && git pull origin main`)
4. Content is updated on production

**Important:** `content/` is excluded from rsync and is not deployed via the code deployment workflow.

#### Scenario 3: Media files uploaded via Panel
1. Media files are uploaded via Kirby Panel on production
2. Files are stored in `media/` and `content/` directories
3. Kirby Git plugin commits the content file changes
4. Media files: Manually download or use FTP/rsync to sync locally if needed
   ```bash
   # Download media from server to local
   rsync -avz user@server:/path/to/media/ ./media/
   ```

## Server Setup

### 1. Install Composer Dependencies

On the production server, ensure Composer dependencies are installed:

```bash
cd /path/to/deployment
composer install --no-dev --optimize-autoloader
```

### 2. Configure Git for Kirby Plugin

The Kirby Git plugin needs Git configured on the server:

```bash
cd /path/to/deployment/content
git config user.email "panel@technikwuerze.de"
git config user.name "Kirby Panel"

# If not already initialized
git init
git remote add origin git@github.com:macx/technikwuerze-content.git
git pull origin main
```

### 3. Setup Git Authentication

For the plugin to push to GitHub, set up SSH key authentication:

```bash
# Generate SSH key on server
ssh-keygen -t ed25519 -C "server@technikwuerze.de"

# Add public key to GitHub
cat ~/.ssh/id_ed25519.pub
# Copy this and add to: GitHub → Settings → SSH and GPG keys → New SSH key
```

Or use a GitHub Personal Access Token:

```bash
# Set up Git credential helper
git config credential.helper store
git config remote.origin.url https://github-token@github.com/macx/technikwuerze.git
```

### 4. Set Environment

Create a `.env` file on the server to set the environment:

```bash
# /path/to/deployment/.env
KIRBY_MODE=production
```

Or configure in your web server:

**Apache (.htaccess):**
```apache
SetEnv KIRBY_MODE production
```

**Nginx:**
```nginx
fastcgi_param KIRBY_MODE production;
```

### 5. File Permissions

Ensure proper permissions:

```bash
# Set ownership (adjust user/group as needed)
chown -R www-data:www-data /path/to/deployment

# Set directory permissions
find /path/to/deployment -type d -exec chmod 755 {} \;

# Set file permissions
find /path/to/deployment -type f -exec chmod 644 {} \;

# Make these writable by web server
chmod -R 775 content media site/cache site/sessions
```

## Manual Deployment

If you need to deploy manually:

```bash
# Build locally
pnpm install
pnpm run build

# Deploy via rsync
rsync -avz --delete \
  --exclude 'node_modules' \
  --exclude '.git' \
  --exclude 'content' \
  --exclude 'media' \
  --exclude 'site/accounts' \
  --exclude 'site/cache' \
  --exclude 'site/sessions' \
  -e "ssh -i ~/.ssh/deploy_key" \
  ./ user@server:/path/to/deployment/
```

## Troubleshooting

### Deployment fails with SSH authentication error

- Verify SSH key is correctly added to GitHub secrets
- Ensure the public key is in server's `~/.ssh/authorized_keys`
- Check file permissions: `chmod 600 ~/.ssh/authorized_keys`

### Kirby Git plugin not pushing

- Check Git is configured on server
- Verify SSH key is added to GitHub (for the server)
- Check `site/config/config.production.php` has `push.enabled => true`
- Review server logs: `tail -f /var/log/apache2/error.log`

### Content not syncing

- Verify the plugin is installed: `composer show thathoff/kirby-git-content`
- Check plugin is enabled in config
- Ensure Git is initialized in `/content` on the server
- Check write permissions on content directories

### Build fails in GitHub Actions

- Check all tests pass locally: `pnpm run test`
- Verify `pnpm-lock.yaml` is committed
- Review workflow logs in GitHub Actions tab

## Rollback

To rollback to a previous deployment:

1. Find the commit hash you want to rollback to
2. SSH into the server
3. Run:
   ```bash
   cd /path/to/deployment
   git fetch origin
   git reset --hard <commit-hash>
   composer install --no-dev
   ```

## Monitoring

- GitHub Actions logs: Repository → Actions tab
- Server logs: Check your web server error logs
- Git plugin activity: Check Git commit history

## Security Notes

- Never commit `.env` files or secrets
- Rotate SSH keys periodically
- Use strong passwords for Kirby Panel accounts
- Keep dependencies updated: `composer update` and `pnpm update`
- Monitor GitHub security alerts

## Support

For issues:
1. Check GitHub Actions logs
2. Review server error logs
3. Check Kirby Git plugin documentation: https://github.com/thathoff/kirby-git-content
4. Verify all secrets are correctly configured in GitHub
