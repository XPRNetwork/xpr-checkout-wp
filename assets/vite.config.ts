import { resolve } from "path";
import { defineConfig } from "vite";
import { svelte } from '@sveltejs/vite-plugin-svelte'
const config = {
  checkout: {
    entry: resolve(__dirname, "./js/public/main.ts"),
    fileName: "woow.public",
  },
  regstore: {
    entry: resolve(__dirname, "./js/admin/regstore/main.ts"),
    fileName: "woow.admin.regstore",
    
  },
};
const currentConfig = config[process.env.LIB_NAME];
if (currentConfig === undefined) {
  throw new Error('LIB_NAME is not defined or is not valid');
}
export default defineConfig({
  plugins: [svelte()],
  
  build: {
    rollupOptions: {
      output: {
        assetFileNames:()=>`${currentConfig.fileName}.[ext]`,
      }
    },
    outDir: `../dist/${process.env.LIB_NAME}`,
    lib: {
      name: `${process.env.LIB_NAME}`,
      fileName:()=>`${process.env.LIB_NAME}.[ext]`,
      ...currentConfig,
      formats: ["iife"],
    },
    emptyOutDir: false,
  },
});