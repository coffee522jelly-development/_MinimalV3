<script lang="ts">
  import { onMount, mount, unmount } from 'svelte';
  import { format } from 'date-fns';
  import { ChevronRight, Home, AlertCircle } from '@lucide/svelte';
  import ReadingTime from './ReadingTime.svelte';
  import TOC from './TOC.svelte';
  import CategoryNav from './CategoryNav.svelte';
  import AppIntro from './templates/AppIntro.svelte';
  import ReleaseNotes from './templates/ReleaseNotes.svelte';
  import DevDiary from './templates/DevDiary.svelte';
  import SEO from './SEO.svelte';
  import CodeBlock from './CodeBlock.svelte';
  import CategoryBadges from './CategoryBadges.svelte';
  import { Button } from './ui/button';

  let { slug } = $props<{ slug: string }>();

  let post = $state<any>(null);
  let loading = $state(true);
  let contentEl = $state<HTMLElement | null>(null);
  let settings = $state<any>(null);

  onMount(async () => {
    fetchData();
  });

  $effect(() => {
    if (slug) fetchData();
  });

  $effect(() => {
    if (post && contentEl) {
      processCodeBlocks();
    }
  });

  async function fetchData() {
    loading = true;
    try {
      const actualSlug = slug.split('/').filter(Boolean).pop() || slug;
      const [postRes, settingsRes] = await Promise.all([
        fetch(`/wp-json/wp/v2/posts?slug=${actualSlug}&_embed`),
        fetch('/wp-json/me/v1/settings')
      ]);

      let data = await postRes.json();
      if (data.length === 0) {
        const pageRes = await fetch(`/wp-json/wp/v2/pages?slug=${actualSlug}&_embed`);
        data = await pageRes.json();
      }

      post = data.length > 0 ? data[0] : null;
      settings = await settingsRes.json();
    } catch (e) {
      console.error(e);
      post = null;
    } finally {
      loading = false;
    }
  }

  function processCodeBlocks() {
    if (!contentEl) return;
    const preBlocks = contentEl.querySelectorAll('pre:not([data-processed])');
    preBlocks.forEach((pre) => {
      const code = pre.querySelector('code');
      if (!code) return;

      const content = code.textContent || "";
      const langClass = Array.from(code.classList).find(c => c.startsWith('language-'));
      const language = langClass ? langClass.replace('language-', '') : 'javascript';

      pre.setAttribute('data-processed', 'true');
      pre.style.display = 'none';

      const container = document.createElement('div');
      pre.parentNode?.insertBefore(container, pre);

      mount(CodeBlock, {
        target: container,
        props: { code: content, language }
      });
    });
  }

  let primaryColor = $derived(settings?.primary_color || '#18181b');
</script>

{#if loading}
  <div class="container py-20 animate-pulse">
    <div class="h-10 bg-muted w-3/4 mb-4"></div>
    <div class="h-4 bg-muted w-1/4 mb-10"></div>
    <div class="space-y-4"><div class="h-4 bg-muted w-full"></div><div class="h-4 bg-muted w-full"></div></div>
  </div>
{:else if post}
  <SEO title={post.title.rendered} type="article" />

  <div class="w-full max-w-[1600px] mx-auto px-4 md:px-8 py-12" style="--primary-color: {primaryColor}">
    <div class="flex flex-col xl:flex-row gap-12 relative">

      <aside class="hidden xl:block w-72 flex-shrink-0">
        <div class="sticky top-24">
          <CategoryNav label={settings?.labels?.categories} />
        </div>
      </aside>

      <article class="flex-1 min-w-0">
        <nav class="flex items-center text-sm text-muted-foreground mb-8 gap-2">
          <a href="/" class="hover:text-primary transition-colors"><Home class="h-4 w-4" /></a>
          <ChevronRight class="h-4 w-4" />
          {#if post.type === 'post'}<a href="/blog" class="hover:text-primary transition-colors">Blog</a><ChevronRight class="h-4 w-4" />{/if}
          <span class="truncate">{@html post.title.rendered}</span>
        </nav>

        <header class="mb-10 max-w-3xl">
          {#if post.categories_data}
            <CategoryBadges categories={post.categories_data} class="mb-6" />
          {/if}
          <h1 class="text-4xl md:text-5xl font-bold mb-6">{@html post.title.rendered}</h1>
          <div class="flex flex-wrap items-center gap-6 text-sm text-muted-foreground border-b pb-6">
            <span>{format(new Date(post.date), 'yyyy.MM.dd')}</span>
            {#if post.type === 'post'}<ReadingTime content={post.content.rendered} label={settings?.labels?.reading_time} />{/if}
          </div>
        </header>

        <div class="max-w-3xl">
          {#if post.meta?._me_template_type === 'app'}<AppIntro {post} />
          {:else if post.meta?._me_template_type === 'release'}<ReleaseNotes {post} />
          {:else if post.meta?._me_template_type === 'diary'}<DevDiary {post} />{/if}

          <div bind:this={contentEl} class="prose dark:prose-invert max-w-none prose-headings:scroll-mt-20 prose-pre:p-0 article-content">
            {@html post.content.rendered}
          </div>

          {#if post.categories_data}
            <footer class="mt-16 pt-8 border-t">
              <p class="text-xs font-bold mb-4 uppercase tracking-widest text-muted-foreground">Posted in</p>
              <CategoryBadges categories={post.categories_data} />
            </footer>
          {/if}
        </div>
      </article>

      <aside class="hidden lg:block xl:w-72 lg:w-64 flex-shrink-0">
        <div class="sticky top-24 space-y-12">
          <TOC content={post.content.rendered} label={settings?.labels?.toc} />
          {#if post.type === 'post'}
            <div class="pt-10 border-t">
              <div class="text-xs font-bold mb-4 uppercase tracking-widest text-muted-foreground">
                {settings?.labels?.article_info || 'Article Info'}
              </div>
              <ReadingTime content={post.content.rendered} label={settings?.labels?.reading_time} />
            </div>
          {/if}
          <div class="xl:hidden border-t pt-10">
             <CategoryNav label={settings?.labels?.categories} />
          </div>
        </div>
      </aside>
    </div>
  </div>
{:else}
  <div class="container py-32 text-center max-w-lg mx-auto">
    <div class="flex justify-center mb-6">
       <div class="bg-destructive/10 p-4 rounded-full"><AlertCircle class="h-12 w-12 text-destructive" /></div>
    </div>
    <h1 class="text-4xl font-bold mb-4">404 - Not Found</h1>
    <p class="text-muted-foreground mb-10 text-lg">Sorry, the page you are looking for does not exist or has been moved.</p>
    <a href="/"><Button variant="default" size="lg">Return to Home</Button></a>
  </div>
{/if}

<style>
  :global(.article-content h2) {
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 0.5rem;
    margin-top: 3rem;
  }
</style>
