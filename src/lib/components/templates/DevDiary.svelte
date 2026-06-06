<script lang="ts">
  import { Book, Calendar, Clock } from '@lucide/svelte';
  import { t, type Language } from '../../../lib/i18n';

  let { post, lang = 'en' } = $props<{ post: any, lang?: Language }>();
  let meta = $derived(post.meta || {});
</script>

<div class="bg-muted/30 rounded-2xl p-6 md:p-8 mb-10 border border-l-4 border-l-primary">
  <div class="flex items-center gap-4 mb-6">
    <div class="bg-primary/10 text-primary p-3 rounded-xl"><Book class="h-6 w-6" /></div>
    <h1 class="text-2xl font-bold">{t('diary', lang)}</h1>
  </div>
  <div class="flex flex-wrap gap-6 text-sm">
    {#if meta._me_diary_date}
      <div class="flex items-center gap-2">
        <Calendar class="h-4 w-4 text-muted-foreground" />
        <span class="font-bold">{t('work_date', lang)}:</span>
        <span>{meta._me_diary_date}</span>
      </div>
    {/if}
    {#if meta._me_diary_hours}
      <div class="flex items-center gap-2">
        <Clock class="h-4 w-4 text-muted-foreground" />
        <span class="font-bold">{t('work_hours', lang)}:</span>
        <span>{meta._me_diary_hours}</span>
      </div>
    {/if}
  </div>
</div>
