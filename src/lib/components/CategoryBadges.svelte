<script lang="ts">
  import { Folder } from '@lucide/svelte';
  import { Badge } from './ui/badge';
  import { cn } from '$lib/utils';

  let { categories, class: className } = $props<{ categories: any[], class?: string }>();
</script>

{#if categories && categories.length > 0}
  <div class={cn("flex flex-wrap gap-2", className)}>
    {#each categories as cat}
      <a href="/category/{cat.slug}">
        <Badge variant="secondary" class="flex items-center gap-1.5 hover:bg-primary hover:text-primary-foreground transition-colors py-1 px-3">
          <Folder class="h-3 w-3" />
          {cat.name}
        </Badge>
      </a>
    {/each}
  </div>
{/if}
