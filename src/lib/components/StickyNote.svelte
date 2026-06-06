<script lang="ts">
  import { cn } from '$lib/utils';

  let { text, color = 'yellow' } = $props<{ text: string, color?: string }>();

  const colorClasses = {
    yellow: "bg-yellow-100 dark:bg-yellow-900/30 border-yellow-200 dark:border-yellow-900/40 text-yellow-900 dark:text-yellow-200",
    blue: "bg-blue-100 dark:bg-blue-900/30 border-blue-200 dark:border-blue-900/40 text-blue-900 dark:text-blue-200",
    pink: "bg-pink-100 dark:bg-pink-900/30 border-pink-200 dark:border-pink-900/40 text-pink-900 dark:text-pink-200",
    green: "bg-green-100 dark:bg-green-900/30 border-green-200 dark:border-green-900/40 text-green-900 dark:text-green-200"
  };

  let activeColorClass = $derived(colorClasses[color as keyof typeof colorClasses] || colorClasses.yellow);
</script>

{#if text}
  <div class={cn(
    "aspect-square w-full flex flex-col p-6 shadow-md border transition-colors duration-300",
    activeColorClass
  )}>
    <div class="font-mono text-sm whitespace-pre-wrap leading-relaxed overflow-y-auto h-full scrollbar-none">
      {text}
    </div>
    <div class="mt-auto pt-4 border-t border-current opacity-20 text-[10px] uppercase tracking-widest font-bold">
      Memo
    </div>
  </div>
{/if}

<style>
  .scrollbar-none::-webkit-scrollbar { display: none; }
  .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
</style>
