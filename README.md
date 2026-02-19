# technikwuerze

Website of the first ever podcast for developers in Germany. Since 2005.

## Tech Stack

- **CMS**: Kirby CMS 5.x (Plainkit)
- **Language**: TypeScript (transpiled via Vite)
- **Build Tool**: Vite 5.x
- **Testing**: Vitest
- **Code Formatting**: Prettier (with PHP support)
- **Package Managers**:
  - Composer for PHP dependencies
  - pnpm for Node dependencies
- **Integration**: kirby-vite plugin for seamless Vite integration

## Prerequisites

- PHP 8.2 or higher
- Node.js (LTS version recommended)
- Composer
- pnpm

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/macx/technikwuerze.git
   cd technikwuerze
   ```

2. Install PHP dependencies:

   ```bash
   composer install
   ```

3. Install Node dependencies:
   ```bash
   pnpm install
   ```

## Development

To start the development server with hot module replacement:

```bash
# Start Vite dev server (processes TypeScript and CSS)
pnpm run dev

# In a separate terminal, start PHP development server
php -S localhost:8000
```

Then open http://localhost:8000 in your browser.

The Vite dev server will automatically:

- Transpile TypeScript to JavaScript
- Process your CSS
- Enable hot module replacement (HMR)
- Watch for changes in Kirby templates, snippets, and content

## Building for Production

To build optimized assets for production:

```bash
pnpm run build
```

This will:

- Run TypeScript type checking
- Validate code formatting with Prettier
- Run all tests with Vitest
- Bundle and minify your TypeScript and CSS
- Generate hashed filenames for cache busting
- Create a manifest.json file for asset loading

## Testing

Run tests:

```bash
pnpm run test        # Run all tests (type-check + format + unit tests)
pnpm run test:watch  # Run tests in watch mode
```

Run individual checks:

```bash
pnpm run type-check      # TypeScript type checking
pnpm run format:check    # Check code formatting
pnpm run format          # Format all files
```

## Code Quality

This project uses:

- **TypeScript** for type safety
- **Prettier** for consistent code formatting (including PHP templates)
- **Vitest** for unit testing
- **EditorConfig** for consistent editor settings

### VS Code Setup

The project includes VS Code settings for automatic formatting on save with Prettier support for:

- TypeScript/JavaScript
- CSS
- JSON
- PHP templates

Install the [Prettier VS Code extension](https://marketplace.visualstudio.com/items?itemName=esbenp.prettier-vscode) for the best experience.

## Project Structure

```
.
├── content/           # Kirby content files
├── dist/             # Built assets (generated, not committed)
├── kirby/            # Kirby CMS core (installed via Composer)
├── media/            # Uploaded media files
├── site/
│   ├── config/       # Kirby configuration
│   ├── plugins/      # Kirby plugins (installed via Composer)
│   ├── templates/    # Kirby templates
│   ├── snippets/     # Reusable template parts
│   └── ...
├── src/              # Source files for Vite
│   ├── index.ts      # Main TypeScript entry point
│   ├── index.css     # Main CSS entry point
│   └── *.test.ts     # Test files
├── vendor/           # PHP dependencies (Composer)
├── .vscode/          # VS Code settings
├── composer.json     # PHP dependencies configuration
├── package.json      # Node dependencies configuration
├── tsconfig.json     # TypeScript configuration
├── vite.config.ts    # Vite configuration
├── vitest.config.ts  # Vitest configuration
├── .prettierrc       # Prettier configuration
├── .editorconfig     # EditorConfig settings
└── index.php         # Kirby entry point
```

## How It Works

The project uses the [kirby-vite plugin](https://github.com/arnoson/kirby-vite) to bridge Kirby CMS and Vite:

- In **development mode**: Assets are loaded from Vite's dev server (http://localhost:5173) with HMR
- In **production mode**: Assets are loaded from the `dist/` directory using the manifest.json file

The plugin automatically detects the mode using a `.dev` file that's created by vite-plugin-kirby.

### TypeScript Support

TypeScript files are automatically transpiled by Vite during development and build. Type checking is performed separately using `tsc` and is integrated into the build pipeline.

### Prettier for PHP Templates

The project uses `@prettier/plugin-php` to format PHP templates consistently. This ensures that Kirby templates maintain the same code quality standards as the TypeScript code.

## Deployment

This project uses automated GitHub Actions workflows for testing and deployment.

### Automated Workflows

- **Tests**: Runs on every push and PR (TypeScript, Prettier, Vitest)
- **Deployment**: Automatically deploys to production on push to `main` branch

### Content Sync

The project uses the `thathoff/kirby-git-content` plugin to automatically commit and sync content changes made via the Kirby Panel. Content uploaded through the panel is automatically versioned in Git and can be pulled locally.

**Content Flow:**
- Panel changes on server → Auto-committed to Git → Push to GitHub → Pull locally
- Local content changes → Push to GitHub → Deployed to server

For detailed deployment setup instructions, see [DEPLOYMENT.md](DEPLOYMENT.md).

### Quick Deployment Setup

1. Configure GitHub Secrets (Settings → Secrets):
   - `DEPLOY_SSH_KEY`: SSH private key for server access
   - `DEPLOY_HOST`: Server hostname
   - `DEPLOY_USER`: SSH username
   - `DEPLOY_PATH`: Deployment directory path

2. Setup Git on production server for content sync
3. Push to `main` branch to trigger deployment

See [DEPLOYMENT.md](DEPLOYMENT.md) for complete setup instructions.

## AI Assistant Setup

This project includes comprehensive instructions for AI coding assistants:

### GitHub Copilot
- Instructions: [.github/copilot-instructions.md](.github/copilot-instructions.md)
- Full project context, architecture, and coding guidelines

### Cursor IDE
- Rules: [.cursorrules](.cursorrules)
- Quick reference for Cursor AI

### VS Code
- Settings: [.vscode/settings.json](.vscode/settings.json)
- Extensions: [.vscode/extensions.json](.vscode/extensions.json)
- Recommended extensions and project-specific configuration

**Key Context for AI:**
- Two separate Git repositories (code + content)
- Hybrid deployment (rsync for code, Git for content)
- TypeScript strict mode with comprehensive testing
- Kirby CMS 5.x specific patterns

## Documentation

- 📖 [README.md](README.md) - This file (project overview)
- 📖 [TODO.md](TODO.md) - Step-by-step deployment setup guide
- 📖 [DEPLOYMENT.md](DEPLOYMENT.md) - Complete deployment documentation
- 📖 [DEPLOYMENT_EXPLAINED.md](DEPLOYMENT_EXPLAINED.md) - Architecture deep-dive
- 📖 [DEPLOYMENT_QUICKREF.md](DEPLOYMENT_QUICKREF.md) - Quick reference commands
- 📖 [PLUGIN_COMPARISON.md](PLUGIN_COMPARISON.md) - Plugin selection rationale
- 📖 [CONTENT_REPOSITORY.md](CONTENT_REPOSITORY.md) - Content repo setup guide
- 🤖 [.github/copilot-instructions.md](.github/copilot-instructions.md) - AI assistant context
- 🤖 [.cursorrules](.cursorrules) - Cursor IDE rules

## License

MIT
