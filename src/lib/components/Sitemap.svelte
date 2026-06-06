<script lang="ts">
  import { onMount } from 'svelte';
  import { t, type Language } from '$lib/i18n';
  import { getRestUrl } from '$lib/api';

  let pages = $state<any[]>([]);
  let categories = $state<any[]>([]);
  let tags = $state<any[]>([]);
  let posts = $state<any[]>([]);
  let settings = $state<any>(null);
  let loading = $state(true);

  onMount(async () => {
    try {
      const [pRes, cRes, tRes, poRes, sRes] = await Promise.all([
        fetch(getRestUrl('wp/v2/pages?per_page=100')),
        fetch(getRestUrl('wp/v2/categories?per_page=100')),
        fetch(getRestUrl('wp/v2/tags?per_page=100')),
        fetch(getRestUrl('wp/v2/posts?per_page=20')),
        fetch(getRestUrl('me/v1/settings'))
      ]);
      pages = await pRes.json();
      categories = await cRes.json();
      tags = await tRes.json();
      posts = await poRes.json();
      settings = await sRes.json();
    } catch (e) { console.error(e); } finally { loading = false; }
  });

  let lang = $derived(settings?.language as Language || 'en');
</script>

<div class="container py-20 max-w-4xl">
  <h1 class="text-4xl font-bold mb-10">{t('sitemap', lang)}</h1>
  {#if !loading}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
      <section>
        <h2 class="text-xl font-bold mb-4 pb-2 border-b">{t('pages', lang)}</h2>
        <ul class="space-y-2">
          <li><a href="/" class="text-primary hover:underline">{t('home', lang)}</a></li>
          {#each pages as page}<li><a href="/{page.slug}" class="text-primary hover:underline">{page.title.rendered}</a></li>{/each}
        </ul>
      </section>
      <section>
        <h2 class="text-xl font-bold mb-4 pb-2 border-b">{t('latest_posts', lang)}</h2>
        <ul class="space-y-2">
          {#each posts as post}<li><a href="/{post.slug}" class="text-primary hover:underline">{post.title.rendered}</a></li>{/each}
        </ul>
      </section>
      <section>
        <h2 class="text-xl font-bold mb-4 pb-2 border-b">{t('categories', lang)}</h2>
        <ul class="flex flex-wrap gap-2">
          {#each categories as cat}<li><a href="/category/{cat.slug}" class="bg-muted px-3 py-1 rounded-full text-sm hover:bg-primary hover:text-primary-foreground transition-colors">{cat.name}</a></li>{/each}
        </ul>
      </section>
      <section>
        <h2 class="text-xl font-bold mb-4 pb-2 border-b">{t('tags', lang)}</h2>
        <ul class="flex flex-wrap gap-2">
          {#each tags as tag}<li><a href="/tag/{tag.slug}" class="text-sm text-muted-foreground hover:text-primary">#{tag.name}</a></li>{/each}
        </ul>
      </section>
    </div>
  {/if}
</div>
