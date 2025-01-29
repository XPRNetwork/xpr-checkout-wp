import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';
import { babel } from '@rollup/plugin-babel';

const createSharedConfig = (appName: string) => {
  return defineConfig({
    plugins: [
      react(),
      babel({
        babelHelpers: 'bundled',
        presets: [
          ['@babel/preset-env', {
            targets: {
              browsers: ['> 0.25%, not dead, IE 11']
            }
          }],
          '@babel/preset-react',
          '@babel/preset-typescript'
        ],
        plugins: [
          '@babel/plugin-transform-runtime'
        ]
      })
    ],
    build: {
      outDir: `../../includes/js/${appName}`,
      lib: {
        entry: resolve(__dirname, `apps/${appName}/src/index.tsx`),
        name: `XPRCheckout${appName.charAt(0).toUpperCase() + appName.slice(1)}`,
        formats: ['iife'],
        fileName: () => 'index.js'
      },
      rollupOptions: {
        external: ['react', 'react-dom'],
        output: {
          globals: {
            react: 'React',
            'react-dom': 'ReactDOM'
          },
          extend: true
        }
      },
      target: 'es5',
      minify: 'terser',
      terserOptions: {
        compress: {
          drop_console: true
        }
      }
    },
    define: {
      'process.env.NODE_ENV': JSON.stringify(process.env.NODE_ENV)
    }
  });
};

export default createSharedConfig;