# GitHub Copilot Instructions for Technikwürze

## Project Overview

Technikwürze is a website for Germany's first developer podcast (since 2005). Built with **Kirby CMS 5.x** + **TypeScript/Vite** + **Automated GitHub Deployment**.

## Architecture

### Two Separate Git Repositories

1. **Main Repository (Code)**: `macx/technikwuerze`
   - PHP templates & configuration
   - TypeScript/CSS source (src/)
   - Vite build configuration
   - GitHub Actions workflows
   - **EXCLUDES**: content/ directory

2. **Content Repository**: `content/` subdirectory
   - **Separate Git repository**
   - Managed by kirby-git-content plugin
   - Panel changes auto-commit/push
   - **NOT deployed via rsync**

### Deployment Architecture

```
┌─────────────────┐         ┌─────────────────┐
│  CODE (rsync)   │         │ CONTENT (Git)   │
├─────────────────┤         ├─────────────────┤
│ • Templates     │         │ • Panel edits   │
│ • Built assets  │         │ • Auto-commit   │
│ • PHP code      │         │ • Auto-push     │
│ • Dependencies  │         │ • Bidirectional │
└─────────────────┘         └─────────────────┘
```

## Tech Stack

- **CMS**: Kirby 5.x (Plainkit)
- **Language**: TypeScript (transpiled by Vite)
- **Build Tool**: Vite 5.x
- **CSS**: Plain CSS (no framework)
- **Testing**: Vitest + TypeScript checking + Prettier
- **Deployment**: GitHub Actions → rsync (code) + Git plugin (content)
- **Package Managers**: 
  - Composer (PHP dependencies)
  - pnpm (Node dependencies)

## Key Plugins

- **arnoson/kirby-vite**: Vite integration
- **thathoff/kirby-git-content**: Content sync via Git

## Development Workflow

### Local Development

```bash
# Start Vite dev server (TypeScript + CSS)
pnpm run dev

# Start PHP server (separate terminal)
php -S localhost:8000

# Run tests
pnpm run test        # Full suite
pnpm run type-check  # TypeScript only
pnpm run format      # Format code
```

### Content Workflow

**On Production:**
1. Editor creates content via Kirby Panel
2. kirby-git-content auto-commits
3. Plugin auto-pushes to GitHub
4. Content is versioned

**Locally:**
```bash
cd content/
git pull  # Get latest content from production
```

**Important:** Content should primarily be edited via Panel, not locally.

### Code Deployment

1. Developer makes changes locally
2. `git push origin main`
3. GitHub Actions runs:
   - TypeScript type checking
   - Prettier format checking
   - Vitest tests
   - Production build
4. rsync deploys to server (excludes content/)

## File Structure

```
.
├── content/              # Separate Git repo (kirby-git-content)
├── dist/                 # Built assets (generated, gitignored)
├── kirby/                # Kirby CMS core (Composer)
├── media/                # Uploaded media
├── site/
│   ├── config/          # Kirby configuration
│   │   ├── config.php   # Base config
│   │   └── config.production.php  # Production overrides
│   ├── plugins/         # Kirby plugins (Composer)
│   ├── templates/       # Kirby PHP templates
│   └── snippets/        # Reusable template parts
├── src/                 # Source files
│   ├── index.ts         # Main TypeScript entry
│   ├── index.css        # Main CSS
│   └── *.test.ts        # Test files
├── .github/workflows/   # GitHub Actions
│   ├── test.yml         # CI tests
│   └── deploy.yml       # Production deployment
├── vendor/              # PHP dependencies (Composer)
└── vite.config.ts       # Vite configuration
```

## Coding Guidelines

### TypeScript

- Use strict mode (tsconfig.json)
- Prefer modern ES2020+ features
- Type everything explicitly
- No `any` types

### PHP (Kirby Templates)

- Follow PSR-2 style
- 4-space indentation
- Use Kirby's helper functions
- Format with Prettier (via @prettier/plugin-php)

### CSS

- Use CSS custom properties (variables)
- Mobile-first approach
- 2-space indentation
- System font stack

### Testing

- Write tests for all TypeScript functions
- Use Vitest with `describe()` and `it()`
- Test filename: `*.test.ts`

## Important Rules

### DO:
✅ Format code with Prettier before committing
✅ Run tests before pushing (`pnpm run test`)
✅ Use TypeScript for all JavaScript
✅ Edit content via Kirby Panel (not directly in content/)
✅ Keep built assets out of Git (dist/ is gitignored)

### DON'T:
❌ Commit content/ to main repository (separate repo!)
❌ Commit built assets (dist/)
❌ Commit node_modules or vendor/
❌ Use JavaScript instead of TypeScript
❌ Edit content files directly (use Panel)
❌ Skip tests before deployment

## rsync Deployment Details

**Deployed via rsync:**
- PHP code & templates
- Built TypeScript/CSS assets (dist/)
- Composer dependencies (vendor/)
- Kirby core (kirby/)

**EXCLUDED from rsync:**
- content/ (managed by Git plugin)
- media/ (uploaded files)
- site/accounts/ (user data)
- site/cache/ (temporary)
- site/sessions/ (temporary)

## Environment Configuration

### Local (Development)
```php
'debug' => true,
'thathoff.git-content' => [
    'commit' => ['enabled' => true],
    'push' => ['enabled' => false],  // Manual push
]
```

### Production
```php
'debug' => false,
'thathoff.git-content' => [
    'commit' => ['enabled' => true],
    'push' => ['enabled' => true],   // Auto-push
]
```

Set via `.env`: `KIRBY_MODE=production`

## Common Tasks

### Add a new page template

1. Create `site/templates/my-template.php`
2. Format with Prettier
3. Test locally
4. Commit and push

### Update styling

1. Edit `src/index.css`
2. Check in browser (HMR active)
3. Run `pnpm run build` to verify
4. Commit and push

### Add TypeScript functionality

1. Edit `src/index.ts` or create new `.ts` file
2. Add types explicitly
3. Write tests in `*.test.ts`
4. Run `pnpm run test`
5. Commit and push

### Sync content from production

```bash
cd content/
git pull origin main
```

## GitHub Actions Secrets

Required for deployment (already configured):
- `DEPLOY_SSH_KEY`: SSH private key
- `DEPLOY_HOST`: Server hostname
- `DEPLOY_USER`: SSH username
- `DEPLOY_PATH`: Deployment directory path

## Documentation

- 📖 [TODO.md](../TODO.md) - Setup guide
- 📖 [DEPLOYMENT.md](../DEPLOYMENT.md) - Deployment details
- 📖 [DEPLOYMENT_EXPLAINED.md](../DEPLOYMENT_EXPLAINED.md) - Architecture explanation
- 📖 [PLUGIN_COMPARISON.md](../PLUGIN_COMPARISON.md) - Plugin choices
- 📖 [README.md](../README.md) - Project overview

## When Helping with Code

1. **Understand the context**: Two separate repos (code + content)
2. **Check file location**: Is it code (main repo) or content (separate)?
3. **Follow conventions**: TypeScript strict mode, Prettier formatting
4. **Consider deployment**: Will it affect rsync or content sync?
5. **Test first**: Always suggest running tests
6. **Document changes**: Update README if architecture changes

## Quick Reference

```bash
# Development
pnpm run dev          # Vite dev server
php -S localhost:8000 # PHP server

# Testing
pnpm run test         # All tests
pnpm run type-check   # TypeScript only
pnpm run format:check # Check formatting

# Building
pnpm run build        # Production build

# Content sync (in content/ directory)
git pull origin main  # Get latest from production
git push origin main  # Push local changes (if any)

# Deployment
git push origin main  # Triggers GitHub Actions
```

## Summary

This is a **hybrid deployment architecture**:
- **Code**: Managed in main repo, deployed via rsync
- **Content**: Managed in separate repo, synced via Git plugin
- **Best of both worlds**: Clean separation, automated workflows

When suggesting code changes, always consider which repository and deployment method applies!
