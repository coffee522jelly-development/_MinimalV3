<script lang="ts">
  import { Folder, FolderOpen, ChevronRight } from '@lucide/svelte';
  import { cn } from '$lib/utils';

  export let node: any;
  export let level: number = 0;
  export let openNodes: Record<number, boolean>;
  export let onToggle: (id: number) => void;

  $: isOpen = openNodes[node.id];
  $: hasChildren = node.children && node.children.length > 0;
</script>

<div class="select-none">
  <div
    class={cn(
      "flex items-center gap-2 py-1 px-2 rounded-md hover:bg-muted transition-colors cursor-pointer group",
      level > 0 && "ml-4 border-l pl-4"
    )}
    on:click={() => hasChildren && onToggle(node.id)}
    role="button"
    tabindex="0"
    on:keydown={(e) => e.key === 'Enter' && hasChildren && onToggle(node.id)}
  >
    {#if hasChildren}
      <ChevronRight class={cn("h-3 w-3 transition-transform", isOpen && "rotate-90")} />
      {#if isOpen}
        <FolderOpen class="h-4 w-4 text-primary" />
      {:else}
        <Folder class="h-4 w-4 text-primary" />
      {/if}
    {:else}
      <div class="w-3"></div>
      <Folder class="h-4 w-4 text-muted-foreground" />
    {/if}

    <a href="/category/{node.slug}" class="flex-1 group-hover:text-primary">
      {node.name}
      <span class="text-[10px] text-muted-foreground ml-1">({node.count})</span>
    </a>
  </div>

  {#if hasChildren && isOpen}
    <div class="animate-in fade-in slide-in-from-left-2 duration-200">
      {#each node.children as child}
        <svelte:self node={child} level={level + 1} {openNodes} {onToggle} />
      {/each}
    </div>
  {/if}
</div>
