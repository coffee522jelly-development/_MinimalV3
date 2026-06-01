<script lang="ts">
  import { onMount } from 'svelte';
  import { Folder } from '@lucide/svelte';
  import CategoryNode from './CategoryNode.svelte';

  let { label = "Categories" } = $props<{ label?: string }>();

  let categories = $state<any[]>([]);
  let openNodes = $state<Record<number, boolean>>({});

  onMount(async () => {
    try {
      const res = await fetch('/wp-json/wp/v2/categories?per_page=100&orderby=name&order=asc');
      categories = await res.json();
    } catch (e) {
      console.error(e);
    }
  });

  interface CategoryTreeItem {
    id: number;
    name: string;
    slug: string;
    count: number;
    children: CategoryTreeItem[];
  }

  function buildTree(parentId = 0, level = 0): CategoryTreeItem[] {
    if (level >= 3) return [];
    return categories
      .filter(cat => cat.parent === parentId)
      .map(cat => ({
        id: cat.id,
        name: cat.name,
        slug: cat.slug,
        count: cat.count,
        children: buildTree(cat.id, level + 1)
      }));
  }

  let tree = $derived(buildTree());

  function toggleNode(id: number) {
    if (openNodes[id]) {
      delete openNodes[id];
    } else {
      openNodes[id] = true;
    }
    openNodes = { ...openNodes };
  }
</script>

<div class="font-mono text-sm bg-muted/20 rounded-lg border p-4">
  <div class="flex items-center gap-2 mb-4 pb-2 border-b">
    <Folder class="h-4 w-4 text-primary" />
    <span class="font-bold">{label || 'Categories'}</span>
  </div>

  <div class="space-y-1">
    {#each tree as node}
      <CategoryNode {node} level={0} {openNodes} onToggle={toggleNode} />
    {/each}
  </div>
</div>
