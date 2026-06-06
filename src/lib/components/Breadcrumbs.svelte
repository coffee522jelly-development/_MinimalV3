<script lang="ts">
  import { onMount } from 'svelte';
  import { ChevronRight, Home } from '@lucide/svelte';
  import { t, type Language } from '$lib/i18n';

  let { slug, post, settings } = $props<{ slug: string, post: any, settings: any }>();
  let categories = $state<any[]>([]);

  onMount(async () => {
    if (post?.categories) {
       // In a real WP API, _embed usually includes terms, but we can fetch if needed
    }
  });

  let lang = $derived(settings?.language as Language || 'en');
</script>

<nav class="flex items-center text-sm text-muted-foreground mb-8 gap-2 overflow-x-auto whitespace-nowrap pb-2 no-scrollbar">
  <a href="/" class="hover:text-primary transition-colors flex-shrink-0"><Home class="h-4 w-4" /></a>
  <ChevronRight class="h-4 w-4 flex-shrink-0" />

  {#if post.type === 'post'}
    <a href="/blog" class="hover:text-primary transition-colors flex-shrink-0">{t('blog', lang)}</a>
    <ChevronRight class="h-4 w-4 flex-shrink-0" />

    {#if post.categories_data && post.categories_data.length > 0}
      <a href="/category/{post.categories_data[0].slug}" class="hover:text-primary transition-colors flex-shrink-0">
        {post.categories_data[0].name}
      </a>
      <ChevronRight class="h-4 w-4 flex-shrink-0" />
    {/if}
  {/if}

  <span class="truncate text-foreground font-medium">{@html post.title.rendered}</span>
</nav>
