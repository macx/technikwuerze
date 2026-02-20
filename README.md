# technikwuerze

Website of the first developer podcast in Germany (Kirby CMS + Vite + TypeScript).

## Stack

- Kirby CMS 5
- Vite 5 + `vite-plugin-kirby`
- TypeScript + Vitest
- Composer (PHP dependencies)
- pnpm (Node dependencies)

## Prerequisites

- PHP 8.2+
- Node.js 20+
- Composer
- pnpm (via Corepack)

## Setup

```bash
git clone https://github.com/macx/technikwuerze.git
cd technikwuerze
composer install
pnpm install
```

## Development (VS Code)

Use **Run and Debug** (German UI: **Ausführen und Debuggen**):

1. Open **Run and Debug**
2. Select `🚀 Development`
3. Press `F5`

What starts automatically:

- PHP server on `http://localhost:8000` (`kirby/router.php`)
- Vite dev server on `http://localhost:5173` (HMR assets)
- Chrome incognito window with:
  - `http://localhost:8000/`
  - `http://localhost:8000/panel`

Important: The actual site runs on `:8000`. `:5173` serves Vite/HMR assets.

## Development (CLI fallback)

```bash
php -S localhost:8000 kirby/router.php
pnpm run dev
```

Then open `http://localhost:8000`.

## Build and Test

```bash
pnpm run test
pnpm run build
```

## Content Repository (separate Git repo)

`content/` is a separate nested Git repository (not a submodule).
It is excluded from the main repository and excluded from rsync deployment.

### New developer setup (standard)

The `technikwuerze-content` repository already exists.
For onboarding, just clone it into `content/`:

```bash
rm -rf content
git clone git@github.com:macx/technikwuerze-content.git content
```

### Keep content up to date

```bash
cd content
git pull origin main
```

## Deployment Notes

- Code is deployed via GitHub Actions + `rsync`.
- `content/`, `media/`, `site/accounts/`, cache and sessions are excluded.
- `site/accounts/` is intentionally not versioned.

## Documentation

- `DEPLOYMENT.md`
- `DEPLOYMENT_EXPLAINED.md`
- `DEPLOYMENT_QUICKREF.md`
- `CONTENT_REPOSITORY.md`
- `PLUGIN_COMPARISON.md`

## License

MIT
