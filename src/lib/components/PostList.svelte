<script lang="ts">
  import { onMount } from 'svelte';
  import { format } from 'date-fns';
  import { Badge } from './ui/badge';
  import { Button } from './ui/button';
  import { List, Columns, Grid3X3, Clock, Tag, Folder } from '@lucide/svelte';
  import StickyNote from './StickyNote.svelte';
  import DeveloperCalendar from './DeveloperCalendar.svelte';
  import { t, type Language } from '$lib/i18n';

  let { slug = "", listType = "all" } = $props<{ slug?: string, listType?: string }>();

  let posts = $state<any[]>([]);
  let loading = $state(true);
  let columns = $state(1);
  let filter = $state('all');
  let settings = $state<any>(null);
  let archiveTitle = $state("");

  onMount(async () => {
    fetchData();
  });

  $effect(() => {
    if (slug || listType) fetchData();
  });

  async function fetchData() {
    loading = true;
    archiveTitle = "";
    try {
      let endpoint = '/wp-json/wp/v2/posts?_embed';
      if (listType === 'category' && slug) {
        const catRes = await fetch(`/wp-json/wp/v2/categories?slug=${slug}`);
        const cats = await catRes.json();
        if (cats.length > 0) { endpoint += `&categories=${cats[0].id}`; archiveTitle = cats[0].name; }
      } else if (listType === 'tag' && slug) {
        const tagRes = await fetch(`/wp-json/wp/v2/tags?slug=${slug}`);
        const tags = await tagRes.json();
        if (tags.length > 0) { endpoint += `&tags=${tags[0].id}`; archiveTitle = tags[0].name; }
      }
      const [pRes, sRes] = await Promise.all([ fetch(endpoint), fetch('/wp-json/me/v1/settings') ]);
      posts = await pRes.json();
      settings = await sRes.json();
      const savedColumns = localStorage.getItem('listColumns');
      if (savedColumns) columns = parseInt(savedColumns);
      else if (settings?.default_columns) columns = settings.default_columns;
    } catch (e) { console.error(e); } finally { loading = false; }
  }

  function setColumns(n: number) { columns = n; localStorage.setItem('listColumns', n.toString()); }

  let filteredPosts = $derived(posts.filter(post => {
    if (filter === 'all') return true;
    return (post.meta?._me_template_type || 'standard') === filter;
  }));

  function getReadingTime(content: string) {
    const text = content.replace(/<[^>]*>/g, '');
    return Math.ceil(text.length / 500);
  }

  let lang = $derived(settings?.language as Language || 'en');
</script>

<div class="container py-10">
  <div class="flex flex-col lg:flex-row gap-12 relative">
    <div class="flex-1 min-w-0">
      {#if archiveTitle}
        <div class="flex items-center gap-3 mb-10 pb-6 border-b">
          {#if listType === 'category'}<Folder class="h-8 w-8 text-primary" />{:else}<Tag class="h-8 w-8 text-primary" />{/if}
          <div>
            <span class="text-xs text-muted-foreground uppercase tracking-widest">{listType === 'category' ? t('categories', lang) : t('tags', lang)}</span>
            <h1 class="text-3xl font-bold">{archiveTitle}</h1>
          </div>
        </div>
      {/if}

      <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div class="flex flex-wrap gap-2">
          <Button variant={filter === 'all' ? 'default' : 'outline'} size="sm" onclick={() => filter = 'all'}>{t('all', lang)}</Button>
          <Button variant={filter === 'standard' ? 'default' : 'outline'} size="sm" onclick={() => filter = 'standard'}>{t('tech', lang)}</Button>
          <Button variant={filter === 'app' ? 'default' : 'outline'} size="sm" onclick={() => filter = 'app'}>{t('apps', lang)}</Button>
          <Button variant={filter === 'release' ? 'default' : 'outline'} size="sm" onclick={() => filter = 'release'}>{t('release', lang)}</Button>
          <Button variant={filter === 'diary' ? 'default' : 'outline'} size="sm" onclick={() => filter = 'diary'}>{t('diary', lang)}</Button>
        </div>

        <div class="flex items-center gap-2 bg-muted p-1 rounded-md">
          <Button variant={columns === 1 ? 'background' : 'ghost'} size="icon" class="h-8 w-8" onclick={() => setColumns(1)}><List class="h-4 w-4" /></Button>
          <Button variant={columns === 2 ? 'background' : 'ghost'} size="icon" class="h-8 w-8" onclick={() => setColumns(2)}><Columns class="h-4 w-4" /></Button>
          <Button variant={columns === 4 ? 'background' : 'ghost'} size="icon" class="h-8 w-8" onclick={() => setColumns(4)}><Grid3X3 class="h-4 w-4" /></Button>
        </div>
      </div>

      {#if loading}
        <div class="grid gap-6" class:grid-cols-1={columns === 1} class:grid-cols-2={columns === 2} class:grid-cols-4={columns === 4}>
          {#each Array(6) as _}<div class="border rounded-lg p-4 animate-pulse"><div class="aspect-video bg-muted rounded-md mb-4"></div><div class="h-6 bg-muted rounded w-3/4 mb-2"></div></div>{/each}
        </div>
      {:else if filteredPosts.length === 0}
        <div class="py-20 text-center border rounded-2xl border-dashed"><p class="text-muted-foreground">{t('no_posts', lang)}</p></div>
      {:else}
        <div class="grid gap-6" class:grid-cols-1={columns === 1} class:grid-cols-2={columns === 2} class:lg:grid-cols-4={columns === 4} class:md:grid-cols-2={columns === 4}>
          {#each filteredPosts as post}
            <article class="group border rounded-lg overflow-hidden bg-card hover:shadow-md transition-shadow">
              <a href="/{post.slug}">
                {#if post.featured_image_url}<div class="aspect-video overflow-hidden"><img src={post.featured_image_url} alt="" class="w-full h-full object-cover transition-transform group-hover:scale-105" /></div>{:else}<div class="aspect-video bg-muted flex items-center justify-center"><span class="text-muted-foreground">No Image</span></div>{/if}
                <div class="p-4">
                  <h3 class="text-xl font-bold mb-2 line-clamp-2">{@html post.title.rendered}</h3>
                  <div class="flex items-center justify-between text-xs text-muted-foreground mt-4">
                    <span>{format(new Date(post.date), 'yyyy.MM.dd')}</span>
                    <span>{getReadingTime(post.content.rendered)} min</span>
                  </div>
                </div>
              </a>
            </article>
          {/each}
        </div>
      {/if}
    </div>

    <aside class="hidden lg:block w-72 flex-shrink-0">
      <div class="sticky top-24 space-y-12">
        {#if settings?.widgets?.sticky_note}<StickyNote text={settings.widgets.sticky_note} />{/if}
        {#if settings?.widgets?.show_calendar}<DeveloperCalendar />{/if}
      </div>
    </aside>
  </div>
</div>
