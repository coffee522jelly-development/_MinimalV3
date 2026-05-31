<script lang="ts">
  import { onMount } from 'svelte';

  let pages: any[] = [];
  let categories: any[] = [];
  let tags: any[] = [];
  let posts: any[] = [];
  let loading = true;

  onMount(async () => {
    try {
      const [pRes, cRes, tRes, poRes] = await Promise.all([
        fetch('/wp-json/wp/v2/pages?per_page=100'),
        fetch('/wp-json/wp/v2/categories?per_page=100'),
        fetch('/wp-json/wp/v2/tags?per_page=100'),
        fetch('/wp-json/wp/v2/posts?per_page=20')
      ]);
      pages = await pRes.json();
      categories = await cRes.json();
      tags = await tRes.json();
      posts = await poRes.json();
    } catch (e) {
      console.error(e);
    } finally {
      loading = false;
    }
  });
</script>

<div class="container py-20 max-w-4xl">
  <h1 class="text-4xl font-bold mb-10">Sitemap</h1>
  {#if !loading}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
      <section>
        <h2 class="text-xl font-bold mb-4 pb-2 border-b">Pages</h2>
        <ul class="space-y-2">
          {#each pages as page}
            <li><a href="/{page.slug}" class="text-primary hover:underline">{page.title.rendered}</a></li>
          {/each}
        </ul>
      </section>
      <section>
        <h2 class="text-xl font-bold mb-4 pb-2 border-b">Latest Posts</h2>
        <ul class="space-y-2">
          {#each posts as post}
            <li><a href="/blog/{post.slug}" class="text-primary hover:underline">{post.title.rendered}</a></li>
          {/each}
        </ul>
      </section>
    </div>
  {/if}
</div>
