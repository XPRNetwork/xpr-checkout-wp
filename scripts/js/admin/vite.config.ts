import { defineConfig } from 'vite'
import { resolve } from 'path'
import { svelte } from '@sveltejs/vite-plugin-svelte'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [svelte()],
  build: {
    rollupOptions: {
      output: {
        assetFileNames:'proton-wc-admin.[ext]'
      }
    },
    assetsDir: './',
    outDir:'../../../dist/admin',
    lib: {
      entry: resolve(__dirname, './main.ts'),
      name: 'protonwcapp',
      // the proper extensions will be added
      fileName: 'proton-wc-admin',
      formats:['iife']
    
    },
    
  }
})
