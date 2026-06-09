import { svelte } from '@sveltejs/vite-plugin-svelte'
import { defineConfig } from 'vite'
import path from 'path'

export default defineConfig({
  plugins: [svelte()],
  resolve: {
    alias: {
      '$lib': path.resolve(__dirname, './src/lib'),
    },
  },
  build: {
    manifest: true,
    cssCodeSplit: true,
    reportCompressedSize: false,
    rollupOptions: {
      output: {
        manualChunks: {
          'vendor-ui': ['@lucide/svelte', 'clsx', 'tailwind-merge'],
          'vendor-prism': ['prismjs'],
          'vendor-date': ['date-fns'],
        }
      }
    }
  }
})
