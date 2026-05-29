import { defineConfig, loadEnv } from 'vite'
import { resolve } from 'path'
import dns from 'node:dns'
import kirby from 'vite-plugin-kirby'

dns.setDefaultResultOrder('verbatim')

export default defineConfig(({ command, mode }) => {
  const env = loadEnv(mode, process.cwd(), '')
  const devHost = env.DEV_HOST || '127.0.0.1'
  const devVitePort = Number(env.DEV_VITE_PORT || 5173)
  const watchContent = env.DEV_WATCH_CONTENT !== 'false'

  return {
    root: '.',
    base: command === 'serve' ? '/' : '/dist/',
    publicDir: false,
    resolve: {
      alias: {
        '@plugins': resolve(__dirname, 'site/plugins'),
      },
    },
    css: {
      devSourcemap: true,
    },

    build: {
      outDir: resolve(__dirname, 'public/dist'),
      assetsDir: '',
      emptyOutDir: !process.env.VITE_WATCH,
      manifest: true,
      rollupOptions: {
        input: {
          main: resolve(__dirname, 'src/index.ts'),
        },
      },
      css: {
        devSourcemap: false,
      },
    },

    server: {
      host: devHost,
      strictPort: true,
      port: devVitePort,
      origin: `http://${devHost}:${devVitePort}`,
      watch: {
        ignored: [
          '**/content/.git/**',
          '**/content/**/_changes/**',
          '**/content/**/*.sqlite',
          '**/content/**/*.mp3',
          '**/content/**/*.m4a',
          '**/content/**/*.wav',
        ],
      },
    },

    plugins: [
      kirby({
        watch: [
          './site/(templates|snippets|controllers|models|layouts)/**/*.php',
          './site/plugins/**/*.php',
          './site/plugins/**/*.html',
          './site/plugins/**/*.css',
          ...(watchContent
            ? [
                './content/**/*.txt',
                './content/**/*.yml',
                './content/**/*.yaml',
                './content/**/*.json',
                '!./content/**/_changes/**',
              ]
            : []),
        ],
      }),
    ],
  }
})
