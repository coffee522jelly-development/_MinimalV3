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

  let { slug } = $props<{ slug: string }>();

  let post = $state<any>(null);
  let loading = $state(true);

  onMount(async () => {
    fetchData();
  });

  $effect(() => {
    if (slug) fetchData();
  });

  async function fetchData() {
    loading = true;
    try {
      let res = await fetch(`/wp-json/wp/v2/posts?slug=${slug}&_embed`);
      let data = await res.json();
      if (data.length === 0) {
        res = await fetch(`/wp-json/wp/v2/pages?slug=${slug}&_embed`);
        data = await res.json();
      }
      post = data.length > 0 ? data[0] : null;
    } catch (e) {
      console.error(e);
      post = null;
    } finally {
      loading = false;
    }
  }
</script>

{#if loading}
  <div class="container py-20 animate-pulse">
    <div class="h-10 bg-muted w-3/4 mb-4"></div>
    <div class="h-4 bg-muted w-1/4 mb-10"></div>
    <div class="space-y-4"><div class="h-4 bg-muted w-full"></div><div class="h-4 bg-muted w-full"></div></div>
  </div>
{:else if post}
  <SEO title={post.title.rendered} type="article" />
  <article class="container py-12 max-w-4xl">
    <nav class="flex items-center text-sm text-muted-foreground mb-8 gap-2">
      <a href="/" class="hover:text-primary"><Home class="h-4 w-4" /></a>
      <ChevronRight class="h-4 w-4" />
      {#if post.type === 'post'}<a href="/blog" class="hover:text-primary">Blog</a><ChevronRight class="h-4 w-4" />{/if}
      <span class="truncate">{@html post.title.rendered}</span>
    </nav>

    <header class="mb-10">
      <h1 class="text-4xl md:text-5xl font-bold mb-6">{@html post.title.rendered}</h1>
      <div class="flex flex-wrap items-center gap-6 text-sm text-muted-foreground border-b pb-6">
        <span>{format(new Date(post.date), 'yyyy.MM.dd')}</span>
        {#if post.type === 'post'}<ReadingTime content={post.content.rendered} />{/if}
      </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
      <div class="lg:col-span-3">
        {#if post.meta?._me_template_type === 'app'}<AppIntro {post} />
        {:else if post.meta?._me_template_type === 'release'}<ReleaseNotes {post} />
        {:else if post.meta?._me_template_type === 'diary'}<DevDiary {post} />{/if}

        <div class="prose dark:prose-invert max-w-none prose-headings:scroll-mt-20 prose-pre:p-0">
          {@html post.content.rendered}
        </div>
      </div>
      <aside class="hidden lg:block space-y-8">
        <CategoryNav />
        <div class="sticky top-24 space-y-6">
          <TOC content={post.content.rendered} />
          {#if post.type === 'post'}
            <div class="pt-6 border-t">
              <ReadingTime content={post.content.rendered} />
            </div>
          {/if}
        </div>
      </aside>
    </div>
  </article>
{:else}
  <div class="container py-20 text-center"><h1 class="text-4xl font-bold mb-4">404</h1><p class="text-muted-foreground">Not found.</p></div>
{/if}
