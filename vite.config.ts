import { defineConfig, loadEnv } from 'vite'
import { resolve } from 'path'
import kirby from 'vite-plugin-kirby'

export default defineConfig(({ command, mode }) => {
  const env = loadEnv(mode, process.cwd(), '')
  const devHost = env.DEV_HOST || 'localhost'
  const devVitePort = Number(env.DEV_VITE_PORT || 5173)

  return {
    root: '.',
    base: command === 'serve' ? '/' : '/dist/',
    publicDir: false,
    resolve: {
      alias: {
        '@plugins': resolve(__dirname, 'site/plugins'),
      },
    },

    build: {
      outDir: resolve(__dirname, 'public/dist'),
      assetsDir: '',
      emptyOutDir: true,
      manifest: 'manifest.json',
      rollupOptions: {
        input: {
          main: resolve(__dirname, 'src/index.ts'),
        },
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
          '**/content/**/*.sqlite',
          '**/content/**/*.mp3',
          '**/content/**/*.m4a',
          '**/content/**/*.wav',
        ],
      },
      hmr: {
        host: devHost,
        clientPort: devVitePort,
        protocol: 'ws',
      },
    },

    plugins: [
      kirby({
        watch: [
          './site/(templates|snippets|controllers|models|layouts)/**/*.php',
          './site/plugins/**/*.php',
          './site/plugins/**/*.html',
          './site/plugins/**/*.css',
          './content/**/*.txt',
          './content/**/*.yml',
          './content/**/*.yaml',
          './content/**/*.json',
        ],
      }),
    ],
  }
})
