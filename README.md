# technikwuerze

Website of the first ever podcast for developers in Germany. Since 2005.

## Tech Stack

- **CMS**: Kirby CMS 5.x (Plainkit)
- **Build Tool**: Vite 5.x
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
# Start Vite dev server (processes CSS and JS)
pnpm run dev

# In a separate terminal, start PHP development server
php -S localhost:8000
```

Then open http://localhost:8000 in your browser.

The Vite dev server will automatically:
- Process your CSS and JavaScript
- Enable hot module replacement (HMR)
- Watch for changes in Kirby templates, snippets, and content

## Building for Production

To build optimized assets for production:

```bash
pnpm run build
```

This will:
- Bundle and minify your CSS and JavaScript
- Generate hashed filenames for cache busting
- Create a manifest.json file for asset loading

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
│   ├── index.js      # Main JavaScript entry point
│   └── index.css     # Main CSS entry point
├── vendor/           # PHP dependencies (Composer)
├── composer.json     # PHP dependencies configuration
├── package.json      # Node dependencies configuration
├── vite.config.js    # Vite configuration
└── index.php         # Kirby entry point
```

## How It Works

The project uses the [kirby-vite plugin](https://github.com/arnoson/kirby-vite) to bridge Kirby CMS and Vite:

- In **development mode**: Assets are loaded from Vite's dev server (http://localhost:5173) with HMR
- In **production mode**: Assets are loaded from the `dist/` directory using the manifest.json file

The plugin automatically detects the mode using a `.dev` file that's created by vite-plugin-kirby.

## License

MIT

