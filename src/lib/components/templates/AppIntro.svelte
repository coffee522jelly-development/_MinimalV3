<script lang="ts">
  import { Globe, Smartphone } from '@lucide/svelte';
  import { Button } from '../ui/button';
  import { Badge } from '../ui/badge';
  import { t, type Language } from '../../../lib/i18n';
  import LazyImage from '../LazyImage.svelte';

  let { post, lang = 'en' } = $props<{ post: any, lang?: Language }>();
  let meta = $derived(post.meta || {});
  let logoUrl = $derived(post.app_logo_url);
</script>

<div class="bg-muted/30 rounded-2xl p-6 md:p-8 mb-10 border shadow-sm">
  <!-- Header: App Name and Version -->
  <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 mb-8">
    <div class="flex items-center gap-5">
      {#if logoUrl}
        <div class="w-20 h-20 md:w-24 md:h-24 rounded-2xl overflow-hidden border bg-background flex-shrink-0 shadow-md">
          <LazyImage src={logoUrl} alt={post.title.rendered} class="w-full h-full object-cover" />
        </div>
      {:else}
        <div class="w-20 h-20 md:w-24 md:h-24 rounded-2xl bg-primary/10 text-primary flex items-center justify-center border border-primary/20 flex-shrink-0 shadow-sm">
          <Smartphone class="h-10 w-10 md:h-12 md:h-12" />
        </div>
      {/if}
      <div class="min-w-0">
        <div class="flex items-center gap-2 mb-1 flex-wrap">
          <Badge variant="outline" class="bg-primary/5 text-primary border-primary/20 text-[10px] uppercase tracking-wider font-bold h-5 px-2">
            {t('app_intro', lang)}
          </Badge>
          {#if meta._me_app_version}
            <Badge variant="secondary" class="font-mono text-[10px] h-5 px-2">{t('version', lang)} {meta._me_app_version}</Badge>
          {/if}
        </div>
        <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-foreground leading-tight">
          {@html post.title.rendered}
        </h1>
      </div>
    </div>
  </div>

  <!-- Content: Subtitle + Links -->
  <div class="space-y-6">
    {#if meta._me_app_subtitle}
      <p class="text-xl md:text-2xl text-muted-foreground font-medium leading-relaxed max-w-2xl">
        {meta._me_app_subtitle}
      </p>
    {/if}

    <div class="flex flex-wrap gap-4 pt-4 border-t border-border/50">
      {#if meta._me_app_link_web}
        <a href={meta._me_app_link_web} target="_blank" rel="noopener noreferrer" class="inline-block">
          <Button size="lg" class="h-12 px-8 font-bold shadow-sm">
            <Globe class="h-5 w-5 mr-2" /> {t('website', lang)}
          </Button>
        </a>
      {/if}
      {#if meta._me_app_link_github}
        <a href={meta._me_app_link_github} target="_blank" rel="noopener noreferrer" class="inline-block">
          <Button variant="outline" size="lg" class="h-12 px-8 font-bold shadow-sm bg-background">
            <span class="">{t('github', lang)}</span>
          </Button>
        </a>
      {/if}
    </div>
  </div>
</div>
