<script lang="ts">
  import { Folder, FolderOpen, ChevronRight } from '@lucide/svelte';
  import { cn } from '$lib/utils';
  import CategoryNode from './CategoryNode.svelte';

  let { node, level = 0, openNodes, onToggle } = $props<{
    node: any;
    level?: number;
    openNodes: Record<number, boolean>;
    onToggle: (id: number) => void;
  }>();

  let isOpen = $derived(!!openNodes[node.id]);
  let hasChildren = $derived(node.children && node.children.length > 0);

  function handleToggle(e: MouseEvent) {
    if (hasChildren) {
      e.preventDefault();
      e.stopPropagation();
      onToggle(node.id);
    }
  }
</script>

<div class="select-none">
  <div
    class={cn(
      "flex items-center gap-2 py-1 px-2 rounded-md hover:bg-muted transition-colors cursor-pointer group",
      level > 0 && "ml-4 border-l pl-4"
    )}
    onclick={handleToggle}
    role="button"
    tabindex="0"
    onkeydown={(e) => e.key === 'Enter' && handleToggle(e as any)}
  >
    {#if hasChildren}
      <ChevronRight class={cn("h-3 w-3 transition-transform duration-200", isOpen && "rotate-90")} />
      {#if isOpen}
        <FolderOpen class="h-4 w-4 text-primary" />
      {:else}
        <Folder class="h-4 w-4 text-primary" />
      {/if}
    {:else}
      <div class="w-3"></div>
      <Folder class="h-4 w-4 text-muted-foreground" />
    {/if}

    <a
      href="/category/{node.slug}"
      class="flex-1 group-hover:text-primary transition-colors"
      onclick={(e) => e.stopPropagation()}
    >
      {node.name}
      <span class="text-[10px] text-muted-foreground ml-1">({node.count})</span>
    </a>
  </div>

  {#if hasChildren && isOpen}
    <div class="animate-in fade-in slide-in-from-left-1 duration-200">
      {#each node.children as child}
        <CategoryNode node={child} level={level + 1} {openNodes} {onToggle} />
      {/each}
    </div>
  {/if}
</div>
