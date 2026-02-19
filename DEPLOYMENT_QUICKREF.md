# Deployment Quick Reference

## GitHub Secrets Setup

Configure in: **Repository → Settings → Secrets and variables → Actions**

| Secret Name | Description | Example |
|-------------|-------------|---------|
| `DEPLOY_SSH_KEY` | SSH private key | Contents of `~/.ssh/deploy_key` |
| `DEPLOY_HOST` | Server hostname | `technikwuerze.de` |
| `DEPLOY_USER` | SSH username | `webuser` |
| `DEPLOY_PATH` | Deployment path | `/var/www/technikwuerze` |

## Generate SSH Key for Deployment

```bash
# Generate key pair
ssh-keygen -t ed25519 -C "github-deploy" -f ~/.ssh/deploy_key

# Add public key to server
ssh-copy-id -i ~/.ssh/deploy_key.pub user@server.com

# Copy private key for GitHub Secret
cat ~/.ssh/deploy_key
```

## Server Setup Checklist

- [ ] Install Git: `apt-get install git`
- [ ] Configure Git user:
  ```bash
  git config --global user.email "panel@technikwuerze.de"
  git config --global user.name "Kirby Panel"
  ```
- [ ] Setup SSH key for GitHub push:
  ```bash
  ssh-keygen -t ed25519 -C "server@technikwuerze.de"
  # Add ~/.ssh/id_ed25519.pub to GitHub
  ```
- [ ] Set environment: Create `.env` with `KIRBY_MODE=production`
- [ ] Install Composer dependencies: `composer install --no-dev`
- [ ] Set permissions:
  ```bash
  chown -R www-data:www-data /path/to/site
  chmod -R 775 content media site/cache site/sessions
  ```

## Content Sync Workflow

### Content Created on Server (via Panel)
```
Panel Edit → Auto-commit → Auto-push to GitHub → git pull locally
```

### Content Created Locally
```
Local Edit → git push → GitHub Deploy → Server Updated
```

### Sync Media Files to Local
```bash
rsync -avz user@server:/path/to/media/ ./media/
```

## Common Commands

### Local Development
```bash
pnpm run dev          # Start dev server
pnpm run test         # Run all tests
pnpm run build        # Build for production
```

### Deploy
```bash
git push origin main  # Triggers auto-deployment
```

### Manual Deployment
```bash
pnpm run build
rsync -avz --delete \
  --exclude 'content' \
  --exclude 'media' \
  --exclude 'node_modules' \
  -e ssh ./ user@server:/path/
```

### Server Commands
```bash
# Clear cache
rm -rf /path/to/site/site/cache/*

# Check Git status
cd /path/to/site && git status

# Pull latest (if auto-push disabled)
cd /path/to/site && git pull origin main
```

## Workflow Triggers

### Test Workflow
- Push to `main` or `develop` branches
- Pull requests to `main`

### Deploy Workflow
- Push to `main` branch only
- Manual trigger via GitHub Actions UI

## Troubleshooting

### Deployment Fails
1. Check GitHub Actions logs
2. Verify SSH key in secrets
3. Test SSH manually: `ssh -i ~/.ssh/key user@host`

### Content Not Syncing
1. Verify plugin installed: `composer show oblik/kirby-git`
2. Check Git configured on server
3. Verify write permissions on content/
4. Check server logs

### Build Fails
1. Run locally: `pnpm run test`
2. Check all dependencies installed
3. Review GitHub Actions logs

## Security Checklist

- [ ] SSH keys are protected (600 permissions)
- [ ] `.env` file is in `.gitignore`
- [ ] Production has `debug => false`
- [ ] Server firewall configured
- [ ] Strong Kirby Panel passwords
- [ ] HTTPS enabled on production

## Documentation

- Full guide: [DEPLOYMENT.md](DEPLOYMENT.md)
- README: [README.md](README.md)
- Kirby Git plugin: https://github.com/OblikStudio/kirby-git

## Support

If issues arise:
1. Check workflow logs in GitHub Actions
2. Review server error logs
3. Verify all secrets configured
4. Test SSH connection manually
5. Check Kirby plugin is active
