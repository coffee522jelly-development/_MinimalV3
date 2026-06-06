<script lang="ts">
  import { onMount, mount, unmount } from 'svelte';
  import { format } from 'date-fns';
  import { ChevronRight, Home, AlertCircle, Calendar } from '@lucide/svelte';
  import { marked } from 'marked';
  import mermaid from 'mermaid';
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

  let { slug } = $props<{ slug: string }>();

  let post = $state<any>(null);
  let loading = $state(true);
  let contentEl = $state<HTMLElement | null>(null);
  let settings = $state<any>(null);

  onMount(async () => {
    mermaid.initialize({ startOnLoad: false, theme: 'default' });
    fetchData();
  });

  $effect(() => { if (slug) fetchData(); });
  $effect(() => { if (post && contentEl) { processContent(); } });

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
    } catch (e) { console.error(e); post = null; } finally { loading = false; }
  }

  async function processContent() {
    if (!contentEl) return;

    // 1. Process Code Blocks
    const preBlocks = contentEl.querySelectorAll('pre:not([data-processed])');
    for (const pre of preBlocks) {
      const code = pre.querySelector('code');
      if (!code) continue;

      const content = code.textContent || "";
      const langClass = Array.from(code.classList).find(c => c.startsWith('language-'));
      const language = langClass ? langClass.replace('language-', '') : 'javascript';

      if (language === 'mermaid') {
        pre.setAttribute('data-processed', 'true');
        const id = `mermaid-${Math.random().toString(36).substr(2, 9)}`;
        const container = document.createElement('div');
        container.className = 'mermaid-container my-8 flex justify-center';
        pre.parentNode?.insertBefore(container, pre);
        pre.remove();

        try {
          const { svg } = await mermaid.render(id, content);
          container.innerHTML = svg;
        } catch (err) {
          container.innerHTML = `<pre class="text-destructive">Mermaid Error: ${err}</pre>`;
        }
      } else {
        pre.setAttribute('data-processed', 'true');
        pre.style.display = 'none';
        const container = document.createElement('div');
        pre.parentNode?.insertBefore(container, pre);
        mount(CodeBlock, { target: container, props: { code: content, language } });
      }
    }
  }

  let processedContent = $derived.by(() => {
    if (!post) return "";
    const raw = post.content.rendered;
    const setting = post.meta?._me_parse_markdown || 'auto';
    let shouldParse = setting === 'on';
    if (setting === 'auto') {
      const mdRegex = /^(#|>\s|\*\s|-\s|\d+\.\s|\[.*\]\(.*\)|!\[.*\]\(.*\)|```)/m;
      const hasHtml = /<[a-z][\s\S]*>/i.test(raw);
      shouldParse = mdRegex.test(raw) && (!hasHtml || raw.startsWith('<p>'));
    }
    if (shouldParse) {
      const clean = raw.replace(/^<p>/, '').replace(/<\/p>$/, '');
      return marked.parse(clean);
    }
    return raw;
  });

  let lang = $derived(settings?.language as Language || 'en');
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
  <article class="w-full max-w-[1600px] mx-auto px-4 md:px-8 py-12" style="--primary-color: {primaryColor}">
    <div class="flex flex-col xl:flex-row gap-12 relative">
      <aside class="hidden xl:block w-72 flex-shrink-0">
        <div class="sticky top-24 space-y-12">
          <CategoryNav label={settings?.labels?.categories || t('categories', lang)} />
          {#if settings?.widgets?.sticky_note}<StickyNote text={settings.widgets.sticky_note} color={settings.widgets.sticky_note_color} />{/if}
        </div>
      </aside>

      <article class="flex-1 min-w-0">
        <Breadcrumbs {slug} {post} {settings} />

        <header class="mb-10 max-w-3xl">
          {#if post.categories_data}<CategoryBadges categories={post.categories_data} class="mb-6" />{/if}
          <h1 class="text-4xl md:text-5xl font-bold mb-6">{@html post.title.rendered}</h1>
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
  :global(.article-content h2) { border-bottom: 2px solid var(--primary-color); padding-bottom: 0.5rem; margin-top: 3rem; }
  :global(.mermaid-container svg) { max-width: 100%; height: auto; }
</style>
