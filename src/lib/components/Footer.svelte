<script lang="ts">
  import { onMount } from 'svelte';

  let pages: any[] = [];
  let settings: any = null;
  let currentYear = new Date().getFullYear();

  onMount(async () => {
    try {
      const [pRes, sRes] = await Promise.all([
        fetch('/wp-json/wp/v2/pages?per_page=100&orderby=menu_order&order=asc'),
        fetch('/wp-json/me/v1/settings')
      ]);
      pages = await pRes.json();
      settings = await sRes.json();
    } catch (e) {
      console.error(e);
    }
  });

  $: hierarchicalPages = pages.filter(p => p.parent === 0).map(parent => ({
    ...parent,
    children: pages.filter(child => child.parent === parent.id)
  }));
</script>

<footer class="border-t bg-muted/50">
  <div class="container py-12">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
      <div class="col-span-1 md:col-span-2">
        <h3 class="text-lg font-bold mb-4">{settings?.logo_text || "Minimal Engineer"}</h3>
        <p class="text-sm text-muted-foreground">© {currentYear} {settings?.logo_text || "Minimal Engineer"}.</p>
      </div>

      <div>
        <h4 class="text-sm font-semibold mb-4">Pages</h4>
        <ul class="space-y-3 text-sm text-muted-foreground">
          <li><a href="/" class="hover:text-primary transition-colors">Home</a></li>
          {#each hierarchicalPages as page}
            <li>
              <a href="/{page.slug}" class="hover:text-primary transition-colors font-medium text-foreground">{page.title.rendered}</a>
              {#if page.children.length > 0}
                <ul class="pl-4 mt-2 space-y-1 border-l">
                  {#each page.children as child}
                    <li><a href="/{child.slug}" class="hover:text-primary transition-colors">{child.title.rendered}</a></li>
                  {/each}
                </ul>
              {/if}
            </li>
          {/each}
        </ul>
      </div>

      <div>
        <h4 class="text-sm font-semibold mb-4">Social</h4>
        <ul class="space-y-2 text-sm text-muted-foreground">
          {#each Object.entries(settings?.sns || {}) as [name, url]}
            {#if url}<li><a href={url} target="_blank" rel="noopener" class="hover:text-primary transition-colors capitalize">{name}</a></li>{/if}
          {/each}
        </ul>
      </div>
    </div>
  </div>
</footer>
