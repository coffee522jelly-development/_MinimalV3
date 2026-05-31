<script lang="ts">
  import { Clock } from '@lucide/svelte';

  export let content: string;

  function calculateReadingTime(html: string) {
    const text = html.replace(/<[^>]*>/g, '');
    const minutes = Math.ceil(text.length / 500);
    return minutes;
  }

  $: readingTime = calculateReadingTime(content);
</script>

<div class="flex items-center text-sm text-muted-foreground bg-muted/30 px-3 py-1.5 rounded-full w-fit">
  <Clock class="h-4 w-4 mr-2" />
  <span>読了目安：{readingTime}分</span>
</div>
