<script lang="ts">
  import { Clock } from '@lucide/svelte';

  let { content, label = "Est. Read Time" } = $props<{ content: string, label?: string }>();

  function calculateReadingTime(html: string) {
    const text = html.replace(/<[^>]*>/g, '');
    return Math.ceil(text.length / 500);
  }

  let readingTime = $derived(calculateReadingTime(content));
</script>

<div class="flex items-center text-sm text-muted-foreground bg-muted/30 px-3 py-1.5 rounded-full w-fit">
  <Clock class="h-4 w-4 mr-2" />
  <span>{label || 'Est. Read Time'}: {readingTime} min</span>
</div>
