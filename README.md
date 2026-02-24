# Technikwürze

Website of the first developer podcast in Germany. This stack relies on Kirby CMS 5, Vite 5 with `vite-plugin-kirby`, and TypeScript. The content is stored in a separate Git repository for better separation of concerns and easier deployment.

## Architecture

```
Main Repository (macx/technikwuerze)
├── Code, templates, assets
└── .gitignore includes: /content

Content Repository (macx/technikwuerze-content)
├── Separate Git repository
├── Managed by kirby-git-content plugin
└── Panel changes → auto-commit → auto-push
```

## Prerequisites

- PHP 8.2+
- Composer
- Node.js 22+ (LTS)
- pnpm (via Corepack)

## Setup

Clone this repository and install dependencies:

```bash
git clone https://github.com/macx/technikwuerze.git
cd technikwuerze
composer install
corepack enable
corepack install
pnpm install
```

Corepack reads the pinned `packageManager` version from `package.json` and provides the correct pnpm version automatically.

### Content Repository

Kirby stores all content in the `content/` directory. To keep it separate from the codebase, we use a separate Git repository. When users make changes in the online Kirby Panel, like adding pages, editing content, or uploading media, those changes are commited automatically to the separate `technikwuerze-content` repository.

To pull the content upfront, clone the `technikwuerze-content` repository into the `content/` directory:

```bash
rm -rf content # if it already exists
git clone git@github.com:macx/technikwuerze-content.git content
```

> Please note: Do not push changes directly to the `content/` repository. Always use the Kirby Panel for content changes, which ensures proper commits and synchronization.

### Keep content up to date

```bash
cd content
git pull origin main
```

## Development (VS Code)

Use the **Run and Debug** panel in Visual Studio Code:

1. Open **Run and Debug**
2. Select `🚀 Development`
3. Click the green play button

![sceenshot development server](docs/development-server.png)

What starts automatically:

- PHP server on `http://localhost:8000` (`kirby/router.php`)
- Vite dev server on `http://localhost:5173` (HMR assets)
- Chrome incognito window with:
  - `http://localhost:8000/`
  - `http://localhost:8000/panel`

> Note: The actual site runs on `:8000`. `:5173` serves Vite/HMR assets.

### Development fallback

If you prefer to start the servers manually, run the following commands in separate terminal windows:

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

## Deployment Notes

- Code is deployed via GitHub Actions + `rsync`.
- `content/`, `media/`, `site/accounts/`, cache and sessions are excluded.
- `site/accounts/` is intentionally not versioned in the main repository.

## License

MIT
