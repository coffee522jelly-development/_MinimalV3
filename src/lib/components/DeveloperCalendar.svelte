<script lang="ts">
  import { onMount } from 'svelte';
  import { format, startOfMonth, endOfMonth, eachDayOfInterval, isSameDay, getDay, addMonths, subMonths } from 'date-fns';
  import { ChevronLeft, ChevronRight, Terminal } from '@lucide/svelte';
  import { Button } from './ui/button';
  import { cn } from '$lib/utils';
  import { t, type Language } from '$lib/i18n';
  import { getRestUrl } from '$lib/api';

  let currentDate = $state(new Date());
  let postDates = $state<Date[]>([]);
  let settings = $state<any>(null);

  onMount(async () => {
    try {
      const [pRes, sRes] = await Promise.all([
        fetch(getRestUrl('wp/v2/posts?per_page=100&_fields=date')),
        fetch(getRestUrl('me/v1/settings'))
      ]);
      const posts = await pRes.json();
      settings = await sRes.json();
      postDates = posts.map((p: any) => new Date(p.date));
    } catch (e) {
      console.error(e);
    }
  });

  let days = $derived.by(() => {
    const start = startOfMonth(currentDate);
    const end = endOfMonth(currentDate);
    const dateInterval = eachDayOfInterval({ start, end });
    const padding = Array(getDay(start)).fill(null);
    return [...padding, ...dateInterval];
  });

  function hasPost(date: Date) { return postDates.some(d => isSameDay(d, date)); }
  function nextMonth() { currentDate = addMonths(currentDate, 1); }
  function handlePrev() { currentDate = subMonths(currentDate, 1); }

  let lang = $derived(settings?.language as Language || 'en');
  let primaryColor = $derived(settings?.primary_color || '#18181b');
</script>

<div class="bg-muted/20 border rounded-lg p-4 font-mono text-xs">
  <div class="flex items-center justify-between mb-4 border-b pb-2">
    <div class="flex items-center gap-2">
      <Terminal class="h-3 w-3 text-primary" />
      <span class="font-bold uppercase tracking-wider">{format(currentDate, 'MMM yyyy')}</span>
    </div>
    <div class="flex gap-1">
      <Button variant="ghost" size="icon" class="h-6 w-6" onclick={handlePrev}><ChevronLeft class="h-3 w-3" /></Button>
      <Button variant="ghost" size="icon" class="h-6 w-6" onclick={nextMonth}><ChevronRight class="h-3 w-3" /></Button>
    </div>
  </div>

  <div class="grid grid-cols-7 gap-1 text-center mb-2 text-muted-foreground font-bold">
    {#each ['S', 'M', 'T', 'W', 'T', 'F', 'S'] as day}<div>{day}</div>{/each}
  </div>

  <div class="grid grid-cols-7 gap-1">
    {#each days as day}
      {#if day}
        {@const active = hasPost(day)}
        <div
          class={cn("aspect-square flex items-center justify-center rounded-sm transition-colors", active ? "text-white font-bold" : "text-foreground")}
          style={active ? `background-color: ${primaryColor}` : ""}
        >
          {format(day, 'd')}
        </div>
      {:else}<div class="aspect-square"></div>{/if}
    {/each}
  </div>

  <div class="mt-4 pt-2 border-t text-[10px] text-muted-foreground flex items-center gap-2">
    <div class="w-2 h-2 rounded-sm" style="background-color: {primaryColor}"></div>
    <span>{t('activity', lang)}</span>
  </div>
</div>
