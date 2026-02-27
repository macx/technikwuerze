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

### Keep content up to date

```bash
cd content
git pull origin main
```

### Use the Kirby Panel

The Kirby Panel is available at `http://localhost:8000/panel` by default. Create an account to log in and manage your content. The panel accounts are NOT in sync with the production server.

> Please note: Do not push changes directly to the `content/` repository. Always use the Kirby Panel for content changes, which ensures proper commits and synchronization.

### Local dev ports (optional)

You can override the default local host/ports for both PHP and Vite.

```bash
cp .env.example .env
```

Then edit `.env`:

```bash
DEV_HOST=localhost
DEV_PHP_PORT=8000
DEV_VITE_PORT=5173
```

VS Code tasks (`Run and Debug`) and Vite use these values automatically.

## Development (VS Code)

Use the **Run and Debug** panel in Visual Studio Code:

1. Open **Run and Debug**
2. Select `🚀 Development`
3. Click the green play button

![sceenshot development server](docs/development-server.png)

What starts automatically:

- PHP server on `http://localhost:8000` by default (`kirby/router.php`)
- Vite dev server on `http://localhost:5173` by default (HMR assets)
- Chrome incognito window with:
  - `http://<DEV_HOST>:<DEV_PHP_PORT>/`
  - `http://<DEV_HOST>:<DEV_PHP_PORT>/panel`

> Note: The actual site runs on `DEV_PHP_PORT`. `DEV_VITE_PORT` serves Vite/HMR assets.

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

- Production pipeline:
  - `CI` workflow validates PR/push.
  - `Create Release Tag` workflow bumps `package.json` version and creates a `vX.Y.Z` tag.
  - `Deploy From Tag` workflow deploys via `rsync` only when a `v*` tag is pushed.
  - `Create Release Tag` uses `RELEASE_TOKEN` secret for pushing version bump + tag.
- Deployment excludes are managed in `.rsyncignore`.
- `content/`, `media/`, `site/accounts/`, cache and sessions are excluded from code deployment.
- `site/accounts/` is intentionally not versioned in the main repository.
- The production `content/` folder must be its own Git repository (`technikwuerze-content`).

## Release and Deploy

1. Develop on `develop` branch and push your commits.
2. Create a Pull Request (PR) to `main` using one of these options:
   - Run `pnpm release` locally\
     (opens `https://github.com/macx/technikwuerze/compare/main...develop` in your browser).
   - Open GitHub manually and create a pull request\
     `develop` -> `main`.
3. Wait until `CI` is green.
4. Merge the pull request to `main`.
5. Open `Actions` -> `Create Release Tag` -> `Run workflow`.
6. Enter `version` without `v` (example: `1.4.0`) and start the workflow.
7. The workflow updates `package.json`, commits the version bump to `main`, creates and pushes tag `v1.4.0`, and creates a GitHub release.
8. Tag push triggers `Deploy From Tag` automatically.
9. Verify production: homepage, panel login, one episode page with player, comments.
   No deployment is triggered by regular commits.
   Deployment only happens from semantic tags (`v*`).

## Binary Data Policy (Audio + SQLite)

- MP3 files are stored centrally in `content/audio/` (not per episode folder).
- Episodes reference audio via `Podcasteraudio: - file://<uuid>`.
- SQLite databases (`content/.db/*.sqlite`, `content/podcaster.sqlite`) are not versioned.
- Git keeps only text/meta files (e.g. `*.txt`, `.gitkeep`), not binaries.

Use `rsync` for binaries and runtime DB files, not Git:

```bash
# pull production runtime data to local
rsync -avz user@server:/path/to/project/content/.db/ ./content/.db/
rsync -avz user@server:/path/to/project/content/audio/*.mp3 ./content/audio/

# push local audio binaries to production (if needed)
rsync -avz ./content/audio/*.mp3 user@server:/path/to/project/content/audio/
```

## License

MIT
