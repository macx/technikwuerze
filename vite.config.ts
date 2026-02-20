import { defineConfig } from 'vite'
import { resolve } from 'path'
import kirby from 'vite-plugin-kirby'

export default defineConfig(({ command }) => ({
  root: '.',
  base: command === 'serve' ? '/' : '/dist/',
  publicDir: false,

  build: {
    outDir: resolve(__dirname, 'dist'),
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
    host: 'localhost',
    strictPort: true,
    port: 5173,
    origin: 'http://localhost:5173',
    hmr: {
      host: 'localhost',
      clientPort: 5173,
      protocol: 'ws',
    },
  },

  plugins: [
    kirby({
      watch: ['./site/(templates|snippets|controllers|models|layouts)/**/*.php', './content/**/*'],
    }),
  ],
}))
