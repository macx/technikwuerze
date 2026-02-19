import { defineConfig } from 'vite'
import { resolve } from 'path'

export default defineConfig({
  root: '.',
  base: '/dist/',
  publicDir: false,
  
  build: {
    outDir: resolve(__dirname, 'public/dist'),
    assetsDir: '',
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'src/index.js'),
        styles: resolve(__dirname, 'src/index.css')
      }
    }
  },
  
  server: {
    strictPort: true,
    port: 5173,
    origin: 'http://localhost:5173'
  },
  
  plugins: []
})
