<script lang="ts">
  import { onMount } from 'svelte';
  import { Check, Copy } from 'lucide-svelte';
  import { Button } from './ui/button';
  import Prism from 'prismjs';
  import 'prismjs/components/prism-typescript';
  import 'prismjs/components/prism-javascript';
  import 'prismjs/components/prism-bash';
  import 'prismjs/components/prism-json';
  import 'prismjs/components/prism-css';
  import 'prismjs/themes/prism-tomorrow.css';

  export let code: string;
  export let language: string = 'javascript';
  export let showLineNumbers: boolean = true;

  let copied = false;
  let highlightedCode = "";

  onMount(() => {
    highlight();
  });

  function highlight() {
    const lang = Prism.languages[language] || Prism.languages.javascript;
    highlightedCode = Prism.highlight(code, lang, language);
  }

  async function copyToClipboard() {
    await navigator.clipboard.writeText(code);
    copied = true;
    setTimeout(() => {
      copied = false;
    }, 2000);
  }

  $: lines = code.split('\n');
</script>

<div class="my-6 overflow-hidden rounded-lg border bg-zinc-950 text-zinc-100 shadow-xl">
  <div class="flex items-center justify-between border-b border-zinc-800 bg-zinc-900/50 px-4 py-2">
    <div class="flex items-center gap-2">
      <div class="flex gap-1.5">
        <div class="h-3 w-3 rounded-full bg-red-500/80"></div>
        <div class="h-3 w-3 rounded-full bg-amber-500/80"></div>
        <div class="h-3 w-3 rounded-full bg-emerald-500/80"></div>
      </div>
      <span class="ml-2 text-xs font-medium text-zinc-400 font-mono">{language}</span>
    </div>

    <Button
      variant="ghost"
      size="icon"
      class="h-8 w-8 text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800"
      on:click={copyToClipboard}
    >
      {#if copied}
        <Check class="h-4 w-4 text-emerald-500" />
      {:else}
        <Copy class="h-4 w-4" />
      {/if}
    </Button>
  </div>

  <div class="relative overflow-x-auto p-4 font-mono text-sm leading-relaxed">
    <div class="flex">
      {#if showLineNumbers}
        <div class="mr-4 flex flex-col text-right text-zinc-600 select-none">
          {#each lines as _, i}
            <span>{i + 1}</span>
          {/each}
        </div>
      {/if}
      <pre class="flex-1 !m-0 !p-0 !bg-transparent"><code class="language-{language}">{@html highlightedCode}</code></pre>
    </div>
  </div>
</div>
