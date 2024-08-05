import { resolve } from "path";
import { defineConfig } from "vite";
import { svelte } from '@sveltejs/vite-plugin-svelte'
import react from '@vitejs/plugin-react';
import { NodeGlobalsPolyfillPlugin } from '@esbuild-plugins/node-globals-polyfill';
import { NodeModulesPolyfillPlugin } from '@esbuild-plugins/node-modules-polyfill';
import polyfillNode from 'rollup-plugin-polyfill-node';



const config = {
  checkout: {
    entry: resolve(__dirname, "./js/public/main.ts"),
    fileName: "xprcheckout.public",
    outdir:'public'
  },
  regstore: {
    entry: resolve(__dirname, "./js/admin/regstore/main.ts"),
    fileName: "xprcheckout.admin.regstore",
    outdir:'admin'

  },
  dashboard: {
    entry: resolve(__dirname, "./js/admin/dashboard/main.ts"),
    fileName: "xprcheckout.admin.dashboard",
    outdir:'admin'
  },
  refund: {
    entry: resolve(__dirname, "./js/admin/refund/main.ts"),
    fileName: "xprcheckout.admin.refund",
    outdir:'admin'
  },
  block: {
    entry: resolve(__dirname, "./js/blocks/block.jsx"),
    fileName: "xprcheckout.block",
    outdir:'blocks'
  },
};
const currentConfig = config[process.env.LIB_NAME];
if (currentConfig === undefined) {
  throw new Error('LIB_NAME is not defined or is not valid');
}
export default defineConfig({
  plugins: [
    svelte(),
    react(), // Assurez-vous d'inclure ce plugin pour transformer le JSX
    polyfillNode(),
  ],
  resolve: {
    extensions: ['.mjs', '.js', '.ts', '.jsx', '.tsx', '.json'],
  },
  
  build: {
    commonjsOptions: {
      transformMixedEsModules: true,
    },
    
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