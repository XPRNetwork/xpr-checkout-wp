import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';
import { babel } from '@rollup/plugin-babel';

// List of apps to build
const apps = ['block', 'checkout', 'refund', 'regstore'];

export default defineConfig({
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
    outDir: '../includes/js',
    emptyOutDir: false,
    rollupOptions: {
      input: Object.fromEntries(
        apps.map(app => [
          app,
          resolve(__dirname, `apps/${app}/src/index.tsx`)
        ])
      ),
      output: {
        entryFileNames: '[name]/index.js',
        format: 'iife',
        name: 'XPRCheckout[name]',
        globals: {
          react: 'React',
          'react-dom': 'ReactDOM'
        }
      },
      external: ['react', 'react-dom']
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