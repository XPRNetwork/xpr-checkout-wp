import { build } from 'vite';
import { fileURLToPath } from 'url';
import { dirname, resolve } from 'path';
import fs from 'fs';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

const apps = ['block', 'checkout', 'refund', 'regstore'];

async function buildApps() {
  for (const app of apps) {
    console.log(`Building ${app}...`);
    try {
      const configFile = resolve(__dirname, `apps/${app}/vite.config.ts`);
      await build({
        configFile,
        mode: 'production'
      });
      console.log(`✅ Built ${app} successfully`);
    } catch (error) {
      console.error(`❌ Error building ${app}:`, error);
      process.exit(1);
    }
  }
}

buildApps();