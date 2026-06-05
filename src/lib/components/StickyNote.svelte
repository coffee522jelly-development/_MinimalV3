<script lang="ts">
  import { Pin } from '@lucide/svelte';
  import { cn } from '$lib/utils';

  let { text } = $props<{ text: string }>();
</script>

{#if text}
  <div class="relative bg-yellow-100 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-900/40 p-6 shadow-sm rotate-1 hover:rotate-0 transition-transform duration-300">
    <div class="absolute -top-3 left-1/2 -translate-x-1/2">
       <Pin class="h-6 w-6 text-red-500 fill-current" />
    </div>
    <div class="font-mono text-sm text-yellow-900 dark:text-yellow-200 whitespace-pre-wrap leading-relaxed">
      {text}
    </div>
  </div>
{/if}
