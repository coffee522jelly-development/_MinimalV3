<script lang="ts">
  import { onMount } from 'svelte';
  import { format } from 'date-fns';
  import { Badge } from './ui/badge';
  import { Button } from './ui/button';
  import { List, Columns, Grid3X3, Clock } from 'lucide-svelte';

  let posts: any[] = [];
  let loading = true;
  let columns = 1;
  let filter = 'all';

  onMount(async () => {
    const savedColumns = localStorage.getItem('listColumns');
    if (savedColumns) columns = parseInt(savedColumns);
    await fetchPosts();
  });

  async function fetchPosts() {
    loading = true;
    try {
      const res = await fetch('/wp-json/wp/v2/posts?_embed');
      posts = await res.json();
    } catch (e) {
      console.error('Failed to fetch posts', e);
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

  function getPostTypeLabel(post: any) {
    const type = post.meta?._me_template_type || 'standard';
    switch (type) {
      case 'app': return 'App';
      case 'release': return 'Release';
      case 'diary': return 'Diary';
      default: return 'Tech';
    }
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
          <div class="h-4 bg-muted rounded w-1/2"></div>
        </div>
      {/each}
    </div>
  {:else}
    <div class="grid gap-6" class:grid-cols-1={columns === 1} class:grid-cols-2={columns === 2} class:lg:grid-cols-4={columns === 4} class:md:grid-cols-2={columns === 4}>
      {#each filteredPosts as post}
        <article class="group border rounded-lg overflow-hidden bg-card hover:shadow-md transition-shadow">
          <a href="/blog/{post.slug}">
            {#if post.featured_image_url}
              <div class="aspect-video overflow-hidden">
                <img src={post.featured_image_url} alt={post.title.rendered} class="w-full h-full object-cover transition-transform group-hover:scale-105" />
              </div>
            {:else}
              <div class="aspect-video bg-muted flex items-center justify-center">
                <span class="text-muted-foreground">No Image</span>
              </div>
            {/if}

            <div class="p-4">
              <div class="flex items-center justify-between mb-2">
                <Badge variant="secondary">{getPostTypeLabel(post)}</Badge>
                <div class="flex items-center text-xs text-muted-foreground">
                  <Clock class="h-3 w-3 mr-1" />
                  {getReadingTime(post.content.rendered)} min
                </div>
              </div>

              <h3 class="text-xl font-bold mb-2 line-clamp-2 group-hover:text-primary transition-colors">
                {@html post.title.rendered}
              </h3>

              <div class="text-sm text-muted-foreground line-clamp-3 mb-4">
                {@html post.excerpt.rendered}
              </div>

              <div class="flex items-center justify-between mt-auto pt-4 border-t text-xs text-muted-foreground">
                <span>{format(new Date(post.date), 'MMM d, yyyy')}</span>
                {#if post.categories_data && post.categories_data.length > 0}
                  <span>🏷 {post.categories_data[0].name}</span>
                {/if}
              </div>
            </div>
          </a>
        </article>
      {/each}
    </div>
  {/if}
</div>
