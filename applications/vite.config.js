import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';
import * as babel from '@babel/standalone';

const app = process.env.APP || 'block';

export default defineConfig({
  plugins: [
    react(),
    {
      name: 'babel-transform',
      transform(code, id) {
        if (id.endsWith('.tsx') || id.endsWith('.ts') || id.endsWith('.jsx') || id.endsWith('.js')) {
          const result = babel.transform(code, {
            presets: ['env', 'react', 'typescript'],
            filename: id,
            sourceMaps: true
          });
          return {
            code: result.code,
            map: result.map
          };
        }
      }
    }
  ],
  build: {
    outDir: `../includes/js/${app}`,
    emptyOutDir: false,
    lib: {
      entry: resolve(__dirname, `apps/${app}/src/index.tsx`),
      name: `XPRCheckout${app.charAt(0).toUpperCase() + app.slice(1)}`,
      formats: ['umd'],
      fileName: () => 'index.js'
    },
    rollupOptions: {
      external: [
        'react', 
        'react-dom',
        'react-dom/client',
        'react/jsx-runtime',
        'xprnkit',
        'xprnkit/build/global.css',
        '@proton/api',
        '@proton/js',
        '@proton/web-sdk',
        'classnames',
        'web-vitals',
        'xprcheckout',
        'xprcheckout/utils/sha256',
        'xprcheckout/services/OrderPayment',
        '@radix-ui/react-dialog',
        '@radix-ui/react-dropdown-menu',
        '@radix-ui/react-select',
        '@radix-ui/react-slot',
        '@radix-ui/react-icons',
        'clsx',
        'tailwind-merge'
      ],
      output: {
        globals: {
          react: 'React',
          'react-dom': 'ReactDOM',
          'react-dom/client': 'ReactDOM',
          'react/jsx-runtime': 'jsxRuntime',
          'xprnkit': 'XPRNKit',
          '@proton/api': 'ProtonAPI',
          '@proton/js': 'ProtonJS',
          '@proton/web-sdk': 'ProtonWebSDK',
          'classnames': 'classNames',
          'web-vitals': 'webVitals',
          'xprcheckout': 'XPRCheckout',
          'xprcheckout/utils/sha256': 'XPRCheckout.utils.sha256',
          'xprcheckout/services/OrderPayment': 'XPRCheckout.services.OrderPayment',
          '@radix-ui/react-dialog': 'RadixDialog',
          '@radix-ui/react-dropdown-menu': 'RadixDropdownMenu',
          '@radix-ui/react-select': 'RadixSelect',
          '@radix-ui/react-slot': 'RadixSlot',
          '@radix-ui/react-icons': 'RadixIcons',
          'clsx': 'clsx',
          'tailwind-merge': 'tailwindMerge'
        }
      }
    },
    target: 'es5',
    minify: false,
    sourcemap: true
  },
  esbuild: false,
  optimizeDeps: {
    include: ['@babel/standalone']
  }
});