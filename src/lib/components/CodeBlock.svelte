<script lang="ts">
  import { onMount } from 'svelte';
  import { Check, Copy } from '@lucide/svelte';
  import { Button } from './ui/button';
  import Prism from 'prismjs';
  // Base languages
  import 'prismjs/components/prism-clike';
  import 'prismjs/components/prism-markup';
  import 'prismjs/components/prism-javascript';
  import 'prismjs/components/prism-typescript';
  import 'prismjs/components/prism-bash';
  import 'prismjs/components/prism-json';
  import 'prismjs/components/prism-css';
  import 'prismjs/components/prism-rust';
  import 'prismjs/components/prism-c';
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
  let settings = $state<any>(null);

  onMount(async () => {
    try {
      const res = await fetch(getRestUrl('me/v1/settings'));
      settings = await res.json();
    } catch (e) {}
  });

  let highlightedCode = $derived.by(() => {
    const grammar = Prism.languages[language] || Prism.languages.clike || Prism.languages.javascript;
    return Prism.highlight(code, grammar, language);
  });

  async function copyToClipboard() {
    await navigator.clipboard.writeText(code);
    copied = true;
    setTimeout(() => { copied = false; }, 2000);
  }

  let lines = $derived(code.split('\n'));
  let bgColor = $derived(settings?.code_block?.bg_color || '#09090b');
  let fontSize = $derived(settings?.code_block?.font_size || '14');
</script>

<div class="my-8 overflow-hidden rounded-xl border border-white/10 shadow-2xl not-prose" style="background-color: {bgColor}; color: #f4f4f5;">
  <div class="flex items-center justify-between bg-gradient-to-b from-white/10 to-transparent px-4 py-3">
    <div class="flex items-center gap-3">
      <div class="flex gap-2">
        <div class="h-3 w-3 rounded-full bg-[#ff5f56] shadow-sm"></div>
        <div class="h-3 w-3 rounded-full bg-[#ffbd2e] shadow-sm"></div>
        <div class="h-3 w-3 rounded-full bg-[#27c93f] shadow-sm"></div>
      </div>
      <span class="ml-2 text-[11px] font-bold text-zinc-400 font-mono tracking-wider uppercase opacity-80">
        {language || 'code'}
      </span>
    </div>
    <div class="flex items-center">
      <Button variant="ghost" size="icon" class="h-8 px-2 w-auto text-zinc-400 hover:text-white hover:bg-white/10 flex items-center gap-1.5 transition-all active:scale-95" onclick={copyToClipboard}>
        {#if copied}
          <Check class="h-3.5 w-3.5 text-emerald-400" />
          <span class="text-[10px] font-bold text-emerald-400 uppercase">Copied!</span>
        {:else}
          <Copy class="h-3.5 w-3.5" />
          <span class="text-[10px] font-bold uppercase">Copy</span>
        {/if}
      </Button>
    </div>
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
