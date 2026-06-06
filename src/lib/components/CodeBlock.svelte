<script lang="ts">
  import { onMount } from 'svelte';
  import { Check, Copy } from '@lucide/svelte';
  import { Button } from './ui/button';
  import Prism from 'prismjs';
  import 'prismjs/components/prism-typescript';
  import 'prismjs/components/prism-javascript';
  import 'prismjs/components/prism-bash';
  import 'prismjs/components/prism-json';
  import 'prismjs/components/prism-css';
  import 'prismjs/components/prism-rust';
  import 'prismjs/components/prism-cpp';
  import 'prismjs/components/prism-python';
  import 'prismjs/components/prism-go';
  import 'prismjs/components/prism-php';
  import 'prismjs/components/prism-ruby';
  import 'prismjs/components/prism-java';
  import 'prismjs/components/prism-sql';
  import 'prismjs/components/prism-markdown';
  import 'prismjs/themes/prism-tomorrow.css';
  import { getRestUrl } from '$lib/api';

  let { code, language = 'javascript', showLineNumbers = true } = $props<{
    code: string;
    language?: string;
    showLineNumbers?: boolean;
  }>();

  let copied = $state(false);
  let highlightedCode = $state("");
  let settings = $state<any>(null);

  onMount(async () => {
    highlight();
    try {
      const res = await fetch(getRestUrl('me/v1/settings'));
      settings = await res.json();
    } catch (e) {}
  });

  function highlight() {
    const lang = Prism.languages[language] || Prism.languages.javascript;
    highlightedCode = Prism.highlight(code, lang, language);
  }

  async function copyToClipboard() {
    await navigator.clipboard.writeText(code);
    copied = true;
    setTimeout(() => { copied = false; }, 2000);
  }

  let lines = $derived(code.split('\n'));
  let bgColor = $derived(settings?.code_block?.bg_color || '#09090b');
  let fontSize = $derived(settings?.code_block?.font_size || '14');
</script>

<div class="my-6 overflow-hidden rounded-lg border shadow-xl" style="background-color: {bgColor}; color: #f4f4f5;">
  <div class="flex items-center justify-between border-b border-white/10 bg-black/20 px-4 py-2">
    <div class="flex items-center gap-2">
      <div class="flex gap-1.5"><div class="h-3 w-3 rounded-full bg-red-500/80"></div><div class="h-3 w-3 rounded-full bg-amber-500/80"></div><div class="h-3 w-3 rounded-full bg-emerald-500/80"></div></div>
      <span class="ml-2 text-xs font-medium text-zinc-400 font-mono">{language}</span>
    </div>
    <Button variant="ghost" size="icon" class="h-8 w-8 text-zinc-400 hover:text-white" onclick={copyToClipboard}>
      {#if copied}<Check class="h-4 w-4 text-emerald-500" />{:else}<Copy class="h-4 w-4" />{/if}
    </Button>
  </div>

  <div class="relative overflow-x-auto p-4 font-mono leading-relaxed" style="font-size: {fontSize}px;">
    <div class="flex">
      {#if showLineNumbers}
        <div class="mr-4 flex flex-col text-right text-zinc-600 select-none">
          {#each lines as _, i}<span>{i + 1}</span>{/each}
        </div>
      {/if}
      <pre data-processed="true" class="flex-1 !m-0 !p-0 !bg-transparent"><code class="language-{language}">{@html highlightedCode}</code></pre>
    </div>
  </div>
</div>

<style>
  :global(pre[class*="language-"]) { background: transparent !important; text-shadow: none !important; }
</style>
