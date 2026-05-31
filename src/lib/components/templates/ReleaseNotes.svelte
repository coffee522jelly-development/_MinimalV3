<script lang="ts">
  import { Tag, Calendar } from '@lucide/svelte';
  import { Badge } from '../ui/badge';

  export let post: any;
  $: meta = post.meta || {};
</script>

<div class="bg-muted/30 rounded-2xl p-6 md:p-8 mb-10 border">
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-4">
      <div class="bg-primary text-primary-foreground p-3 rounded-xl"><Tag class="h-6 w-6" /></div>
      <h1 class="text-3xl font-bold">Release Notes</h1>
    </div>
    <div class="flex flex-col md:items-end gap-2">
      {#if meta._me_release_version}<Badge variant="default">v{meta._me_release_version}</Badge>{/if}
    </div>
  </div>
</div>
