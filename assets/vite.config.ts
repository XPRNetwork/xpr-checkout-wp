import { resolve } from "path";
import { defineConfig } from "vite";
import { svelte } from '@sveltejs/vite-plugin-svelte'
const config = {
  checkout: {
    entry: resolve(__dirname, "./js/public/main.ts"),
    fileName: "wookey.public",
    outdir:'public'
  },
  regstore: {
    entry: resolve(__dirname, "./js/admin/regstore/main.ts"),
    fileName: "wookey.admin.regstore",
    outdir:'admin'

  },
  dashboard: {
    entry: resolve(__dirname, "./js/admin/dashboard/main.ts"),
    fileName: "wookey.admin.dashboard",
    outdir:'admin'
  },
  refund: {
    entry: resolve(__dirname, "./js/admin/refund/main.ts"),
    fileName: "wookey.admin.refund",
    outdir:'admin'
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
        
        assetFileNames: () => { console.log(currentConfig.fileName); return `${currentConfig.fileName}.[ext]`},
      },
      
    },
    outDir: `../dist/${currentConfig.outdir}/${process.env.LIB_NAME}`,
    lib: {
      name: `${process.env.LIB_NAME}`,
      fileName:()=>`${process.env.LIB_NAME}.[ext]`,
      ...currentConfig,
      formats: ["iife"],
    },
    emptyOutDir: false,
  },
});