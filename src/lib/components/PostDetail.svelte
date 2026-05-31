<script lang="ts">
  import { onMount } from 'svelte';
  import { format } from 'date-fns';
  import { ChevronRight, Home } from '@lucide/svelte';
  import ReadingTime from './ReadingTime.svelte';
  import TOC from './TOC.svelte';
  import CategoryNav from './CategoryNav.svelte';
  import AppIntro from './templates/AppIntro.svelte';
  import ReleaseNotes from './templates/ReleaseNotes.svelte';
  import DevDiary from './templates/DevDiary.svelte';
  import SEO from './SEO.svelte';

  export let slug: string;

  let post: any = null;
  let loading = true;

  onMount(async () => {
    fetchData();
  });

  $: if (slug) fetchData();

  async function fetchData() {
    loading = true;
    try {
      // Try fetching as post first
      let res = await fetch(`/wp-json/wp/v2/posts?slug=${slug}&_embed`);
      let data = await res.json();

      if (data.length === 0) {
        // Try fetching as page
        res = await fetch(`/wp-json/wp/v2/pages?slug=${slug}&_embed`);
        data = await res.json();
      }

      if (data.length > 0) {
        post = data[0];
      } else {
        post = null;
      }
    } catch (e) {
      console.error(e);
      post = null;
    } finally {
      loading = false;
    }
  }

  function getTemplate(post: any) {
    return post.meta?._me_template_type || 'standard';
  }
</script>

{#if loading}
  <div class="container py-20 animate-pulse">
    <div class="h-10 bg-muted w-3/4 mb-4"></div>
    <div class="h-4 bg-muted w-1/4 mb-10"></div>
    <div class="space-y-4">
      <div class="h-4 bg-muted w-full"></div>
      <div class="h-4 bg-muted w-full"></div>
      <div class="h-4 bg-muted w-2/3"></div>
    </div>
  </div>
{:else if post}
  <SEO
    title={post.title.rendered}
    description={post.excerpt?.rendered.replace(/<[^>]*>/g, '') || ""}
    image={post.featured_image_url}
    type="article"
  />

  <article class="container py-12 max-w-4xl">
    <nav class="flex items-center text-sm text-muted-foreground mb-8 gap-2">
      <a href="/" class="hover:text-primary"><Home class="h-4 w-4" /></a>
      <ChevronRight class="h-4 w-4" />
      {#if post.type === 'post'}
        <a href="/blog" class="hover:text-primary">Blog</a>
        <ChevronRight class="h-4 w-4" />
      {/if}
      <span class="truncate">{@html post.title.rendered}</span>
    </nav>

    <header class="mb-10">
      {#if post.type === 'post'}
        <div class="flex flex-wrap gap-2 mb-4">
          {#each post.categories_data || [] as cat}
            <span class="text-xs font-mono text-primary">🏷 {cat.name}</span>
          {/each}
        </div>
      {/if}

      <h1 class="text-4xl md:text-5xl font-bold mb-6">{@html post.title.rendered}</h1>

      <div class="flex flex-wrap items-center gap-6 text-sm text-muted-foreground border-b pb-6">
        <div class="flex items-center">
          <span>{format(new Date(post.date), 'yyyy.MM.dd')}</span>
        </div>
        {#if post.type === 'post'}
          <ReadingTime content={post.content.rendered} />
        {/if}
      </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
      <div class="lg:col-span-3">
        {#if getTemplate(post) === 'app'}
          <AppIntro {post} />
        {:else if getTemplate(post) === 'release'}
          <ReleaseNotes {post} />
        {:else if getTemplate(post) === 'diary'}
          <DevDiary {post} />
        {/if}

        <div class="prose dark:prose-invert max-w-none prose-pre:p-0">
          {@html post.content.rendered}
        </div>
      </div>

      <aside class="hidden lg:block space-y-8">
        <CategoryNav />
        <div class="sticky top-24">
          <TOC content={post.content.rendered} />
        </div>
      </aside>
    </div>
  </article>
{:else}
  <div class="container py-20 text-center">
    <h1 class="text-4xl font-bold mb-4">404</h1>
    <p class="text-muted-foreground">Post or Page not found.</p>
  </div>
{/if}
