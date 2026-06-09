<script lang="ts">
  import { Globe, Smartphone } from '@lucide/svelte';
  import { Button } from '../ui/button';
  import { t, type Language } from '../../../lib/i18n';
  import LazyImage from '../LazyImage.svelte';

  let { post, lang = 'en' } = $props<{ post: any, lang?: Language }>();
  let meta = $derived(post.meta || {});
  let logoUrl = $derived(post.app_logo_url);
</script>

<div class="bg-muted/30 rounded-2xl p-6 md:p-8 mb-10 border">
  <div class="flex flex-col md:flex-row gap-8">
    {#if logoUrl}
      <div class="w-24 h-24 rounded-2xl overflow-hidden bg-background border flex-shrink-0">
        <LazyImage src={logoUrl} alt={post.title.rendered} class="w-full h-full" />
      </div>
    {/if}
    <div class="flex-1">
      <h1 class="text-3xl font-bold">{@html post.title.rendered}</h1>
      {#if meta._me_app_subtitle}<p class="text-xl text-muted-foreground mb-4">{meta._me_app_subtitle}</p>{/if}
      <div class="flex flex-wrap gap-3 mt-6">
        {#if meta._me_app_link_web}<a href={meta._me_app_link_web}><Button><Globe class="h-4 w-4 mr-2" /> {t('website', lang)}</Button></a>{/if}
        {#if meta._me_app_link_github}<a href={meta._me_app_link_github}><Button variant="outline">{t('github', lang)}</Button></a>{/if}
      </div>
    </div>
  </div>
</div>
