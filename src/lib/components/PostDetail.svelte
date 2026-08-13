<script lang="ts">
  import { onMount, mount } from 'svelte';
  import { format } from 'date-fns';
  import { ChevronRight, Home, AlertCircle, Calendar } from '@lucide/svelte';
  import ReadingTime from './ReadingTime.svelte';
  import TOC from './TOC.svelte';
  import CategoryNav from './CategoryNav.svelte';
  import AppIntro from './templates/AppIntro.svelte';
  import ReleaseNotes from './templates/ReleaseNotes.svelte';
  import DevDiary from './templates/DevDiary.svelte';
  import SEO from './SEO.svelte';
  import CodeBlock from './CodeBlock.svelte';
  import CategoryBadges from './CategoryBadges.svelte';
  import StickyNote from './StickyNote.svelte';
  import DeveloperCalendar from './DeveloperCalendar.svelte';
  import Breadcrumbs from './Breadcrumbs.svelte';
  import { Button } from './ui/button';
  import { t, type Language } from '$lib/i18n';
  import { getRestUrl } from '$lib/api';

  let { slug } = $props<{ slug: string }>();

  let post = $state<any>(null);
  let loading = $state(true);
  let contentEl = $state<HTMLElement | null>(null);
  let settings = $state<any>(null);

  onMount(async () => {
    fetchData();
  });

  $effect(() => { if (slug) fetchData(); });
  $effect(() => { if (post && contentEl) { processContent(); } });

  async function fetchData() {
    loading = true;
    try {
      const actualSlug = slug.split('/').filter(Boolean).pop() || slug;
      const [postRes, settingsRes] = await Promise.all([
        fetch(getRestUrl(`wp/v2/posts?slug=${actualSlug}&_embed`)),
        fetch(getRestUrl('me/v1/settings'))
      ]);
      let data = await postRes.json();
      if (data.length === 0) {
        const pageRes = await fetch(getRestUrl(`wp/v2/pages?slug=${actualSlug}&_embed`));
        data = await pageRes.json();
      }
      post = data.length > 0 ? data[0] : null;
      settings = await settingsRes.json();
    } catch (e) { console.error(e); post = null; } finally { loading = false; }
  }

  async function processContent() {
    if (!contentEl) return;

    // Process standard WP code blocks and raw pre tags
    const blocks = contentEl.querySelectorAll('pre:not([data-processed]), .wp-block-code:not([data-processed])');

    blocks.forEach((block) => {
      try {
        if (block.getAttribute('data-processed')) return;

        const pre = block.tagName === 'PRE' ? block : block.querySelector('pre');
        if (!pre) return;
        if (pre.getAttribute('data-processed')) return;

        const code = pre.querySelector('code');
        const content = (code ? code.textContent : pre.textContent) || "";

        // Determine language
        let language = '';
        if (code) {
          const langClass = Array.from(code.classList).find(c => (c as string).startsWith('language-'));
          if (langClass) language = (langClass as string).replace('language-', '');
        }

        // Mark as processed
        block.setAttribute('data-processed', 'true');
        pre.setAttribute('data-processed', 'true');

        // Hide original and mount custom component
        (pre as HTMLElement).style.display = 'none';
        if (block !== pre) (block as HTMLElement).style.display = 'none';

        const container = document.createElement('div');
        block.parentNode?.insertBefore(container, block);
        mount(CodeBlock, { target: container, props: { code: content.trim(), language } });
      } catch (e) {
        console.error('Error processing code block:', e);
      }
    });
  }

  let processedContent = $derived(post ? post.content.rendered : "");

  let lang = $derived(settings?.language as Language || 'en');
  let primaryColor = $derived(settings?.primary_color || '#18181b');
  let headerSize = $derived(settings?.typography?.header_size || '36');
  let h1Size = $derived(settings?.typography?.h1_size || '36');
  let h2Size = $derived(settings?.typography?.h2_size || '30');
  let h3Size = $derived(settings?.typography?.h3_size || '24');

  function getCSSSize(size: string | number) {
    if (!size) return '';
    return /^\d+(\.\d+)?$/.test(String(size)) ? `${size}px` : String(size);
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
  <article class="w-full max-w-[1600px] mx-auto px-4 md:px-8 py-12" style="--primary-color: {primaryColor}; --header-size: {getCSSSize(headerSize)}; --h1-size: {getCSSSize(h1Size)}; --h2-size: {getCSSSize(h2Size)}; --h3-size: {getCSSSize(h3Size)};">
    <div class="flex flex-col xl:flex-row gap-12 relative">
      <aside class="hidden xl:block w-72 flex-shrink-0">
        <div class="sticky top-24 space-y-12">
          <CategoryNav label={settings?.labels?.categories || t('categories', lang)} />
          {#if settings?.widgets?.show_sticky_note && settings?.widgets?.sticky_note}<StickyNote text={settings.widgets.sticky_note} color={settings.widgets.sticky_note_color} />{/if}
        </div>
      </aside>

      <article class="flex-1 min-w-0">
        <Breadcrumbs {slug} {post} {settings} />

        <header class="mb-10 max-w-3xl">
          {#if post.categories_data}<CategoryBadges categories={post.categories_data} class="mb-6" />{/if}
          {#if post.meta?._me_template_type !== 'app'}
            <h1 class="font-bold mb-6" style="font-size: var(--header-size);">{@html post.title.rendered}</h1>
          {/if}
          <div class="flex flex-wrap items-center gap-x-8 gap-y-4 text-sm text-muted-foreground border-b pb-6">
            <div class="flex items-center gap-2"><Calendar class="h-4 w-4" /><span>{t('published', lang)}: {format(new Date(post.date), 'yyyy.MM.dd')}</span></div>
            <div class="flex items-center gap-2"><Calendar class="h-4 w-4" /><span>{t('updated', lang)}: {format(new Date(post.modified), 'yyyy.MM.dd')}</span></div>
            {#if post.type === 'post'}<ReadingTime content={processedContent} label={settings?.labels?.reading_time || t('reading_time', lang)} />{/if}
          </div>
        </header>

        <div class="max-w-3xl">
          {#if post.meta?._me_template_type === 'app'}<AppIntro {post} lang={lang} />
          {:else if post.meta?._me_template_type === 'release'}<ReleaseNotes {post} lang={lang} />
          {:else if post.meta?._me_template_type === 'diary'}<DevDiary {post} lang={lang} />{/if}
          <div bind:this={contentEl} class="prose dark:prose-invert max-w-none prose-headings:scroll-mt-20 prose-pre:p-0 article-content">{@html processedContent}</div>
          {#if post.categories_data}<footer class="mt-16 pt-8 border-t"><p class="text-xs font-bold mb-4 uppercase tracking-widest text-muted-foreground">{t('posted_in', lang)}</p><CategoryBadges categories={post.categories_data} /></footer>{/if}
        </div>
      </article>

      <aside class="hidden lg:block xl:w-72 lg:w-64 flex-shrink-0">
        <div class="sticky top-24 space-y-12">
          <TOC content={processedContent} label={settings?.labels?.toc || t('toc', lang)} />
          {#if post.type === 'post'}
            <div class="pt-10 border-t">
              <div class="text-xs font-bold mb-4 uppercase tracking-widest text-muted-foreground">{settings?.labels?.article_info || t('article_info', lang)}</div>
              <ReadingTime content={processedContent} label={settings?.labels?.reading_time || t('reading_time', lang)} />
            </div>
          {/if}
          {#if settings?.widgets?.show_calendar}<div class="border-t pt-10"><DeveloperCalendar /></div>{/if}
          <div class="xl:hidden border-t pt-10"><CategoryNav label={settings?.labels?.categories || t('categories', lang)} /></div>
        </div>
      </aside>
    </div>
  </article>
{:else}
  <div class="container py-32 text-center max-w-lg mx-auto">
    <div class="flex justify-center mb-6"><div class="bg-destructive/10 p-4 rounded-full"><AlertCircle class="h-12 w-12 text-destructive" /></div></div>
    <h1 class="text-4xl font-bold mb-4">{t('not_found_title', lang)}</h1>
    <p class="text-muted-foreground mb-10 text-lg">{t('not_found_desc', lang)}</p>
    <a href="/"><Button variant="default" size="lg">{t('return_home', lang)}</Button></a>
  </div>
{/if}

<style>
  :global(.article-content h1) { font-size: var(--h1-size); }
  :global(.article-content h2) { font-size: var(--h2-size); border-bottom: 2px solid var(--primary-color); padding-bottom: 0.5rem; margin-top: 3rem; }
  :global(.article-content h3) { font-size: var(--h3-size); }
</style>
