<script lang="ts">
  import { onMount } from 'svelte';
  import { format } from 'date-fns';
  import { Badge } from './ui/badge';
  import { Button } from './ui/button';
  import { List, Columns, Grid3X3, Clock } from '@lucide/svelte';

  export let slug: string = ""; // For category/tag filtering

  let posts: any[] = [];
  let loading = true;
  let columns = 1;
  let filter = 'all';
  let settings: any = null;

  onMount(async () => {
    fetchPosts();
  });

  $: if (slug) fetchPosts();

  async function fetchPosts() {
    loading = true;
    try {
      let url = '/wp-json/wp/v2/posts?_embed';
      // Basic implementation for category/tag filtering if needed

      const [pRes, sRes] = await Promise.all([
        fetch(url),
        fetch('/wp-json/me/v1/settings')
      ]);
      posts = await pRes.json();
      settings = await sRes.json();

      const savedColumns = localStorage.getItem('listColumns');
      if (savedColumns) {
        columns = parseInt(savedColumns);
      } else if (settings?.default_columns) {
        columns = settings.default_columns;
      }
    } catch (e) {
      console.error(e);
    } finally {
      loading = false;
    }
  }

  function setColumns(n: number) {
    columns = n;
    localStorage.setItem('listColumns', n.toString());
  }

  $: filteredPosts = posts.filter(post => {
    if (filter === 'all') return true;
    const type = post.meta?._me_template_type || 'standard';
    return type === filter;
  });

  function getReadingTime(content: string) {
    const text = content.replace(/<[^>]*>/g, '');
    const minutes = Math.ceil(text.length / 500);
    return minutes;
  }
</script>

<div class="container py-10">
  <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
    <div class="flex flex-wrap gap-2">
      <Button variant={filter === 'all' ? 'default' : 'outline'} size="sm" on:click={() => filter = 'all'}>All</Button>
      <Button variant={filter === 'standard' ? 'default' : 'outline'} size="sm" on:click={() => filter = 'standard'}>Tech</Button>
      <Button variant={filter === 'app' ? 'default' : 'outline'} size="sm" on:click={() => filter = 'app'}>Apps</Button>
      <Button variant={filter === 'release' ? 'default' : 'outline'} size="sm" on:click={() => filter = 'release'}>Release</Button>
      <Button variant={filter === 'diary' ? 'default' : 'outline'} size="sm" on:click={() => filter = 'diary'}>Diary</Button>
    </div>

    <div class="flex items-center gap-2 bg-muted p-1 rounded-md">
      <Button variant={columns === 1 ? 'background' : 'ghost'} size="icon" class="h-8 w-8" on:click={() => setColumns(1)}>
        <List class="h-4 w-4" />
      </Button>
      <Button variant={columns === 2 ? 'background' : 'ghost'} size="icon" class="h-8 w-8" on:click={() => setColumns(2)}>
        <Columns class="h-4 w-4" />
      </Button>
      <Button variant={columns === 4 ? 'background' : 'ghost'} size="icon" class="h-8 w-8" on:click={() => setColumns(4)}>
        <Grid3X3 class="h-4 w-4" />
      </Button>
    </div>
  </div>

  {#if loading}
    <div class="grid gap-6" class:grid-cols-1={columns === 1} class:grid-cols-2={columns === 2} class:grid-cols-4={columns === 4}>
      {#each Array(6) as _}
        <div class="border rounded-lg p-4 animate-pulse">
          <div class="aspect-video bg-muted rounded-md mb-4"></div>
          <div class="h-6 bg-muted rounded w-3/4 mb-2"></div>
        </div>
      {/each}
    </div>
  {:else}
    <div class="grid gap-6" class:grid-cols-1={columns === 1} class:grid-cols-2={columns === 2} class:lg:grid-cols-4={columns === 4} class:md:grid-cols-2={columns === 4}>
      {#each filteredPosts as post}
        <article class="group border rounded-lg overflow-hidden bg-card hover:shadow-md transition-shadow">
          <a href="/{post.slug}">
            {#if post.featured_image_url}
              <div class="aspect-video overflow-hidden">
                <img src={post.featured_image_url} alt="" class="w-full h-full object-cover transition-transform group-hover:scale-105" />
              </div>
            {:else}
              <div class="aspect-video bg-muted flex items-center justify-center">
                <span class="text-muted-foreground">No Image</span>
              </div>
            {/if}
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
