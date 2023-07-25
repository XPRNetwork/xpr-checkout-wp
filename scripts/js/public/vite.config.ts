import { defineConfig } from 'vite'
import { resolve } from 'path'
import { svelte } from '@sveltejs/vite-plugin-svelte'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [svelte()],
  build: {
    rollupOptions: {
      output: {
        //assetFileNames:'woow-public.[ext]'
        assetFileNames: (assetInfo) => {
          let extType = assetInfo.name.split('.').at(1);
          if (/png|jpe?g|svg|gif|tiff|bmp|ico/i.test(extType)) {
            extType = 'img';
          }
          return `public/${extType}/woow-public.[ext]`;
        },
      }
    },
    assetsDir: './',
    outDir:'../../../dist/',
    lib: {
      entry: resolve(__dirname, './main.ts'),
      name: 'woow_public',
      
      fileName: (fileInfo) => { 

        return 'public/js/woow-public.js'

      },
      formats:['iife']
    
    },
    
  }
})
