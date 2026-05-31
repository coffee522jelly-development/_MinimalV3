<script lang="ts">
  import { Book, Calendar, Clock } from '@lucide/svelte';
  export let post: any;
  $: meta = post.meta || {};
</script>

<div class="bg-muted/30 rounded-2xl p-6 md:p-8 mb-10 border border-l-4 border-l-primary">
  <div class="flex items-center gap-4 mb-6">
    <div class="bg-primary/10 text-primary p-3 rounded-xl"><Book class="h-6 w-6" /></div>
    <h1 class="text-2xl font-bold">Development Diary</h1>
  </div>
  <div class="flex flex-wrap gap-6">
    {#if meta._me_diary_date}<span>{meta._me_diary_date}</span>{/if}
    {#if meta._me_diary_hours}<span>{meta._me_diary_hours} hours</span>{/if}
  </div>
</div>
