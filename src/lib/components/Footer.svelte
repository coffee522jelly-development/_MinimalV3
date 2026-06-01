<script lang="ts">
  import { onMount } from 'svelte';

  let menuItems = $state<any[]>([]);
  let settings = $state<any>(null);
  let currentYear = new Date().getFullYear();

  onMount(async () => {
    try {
      const [mRes, sRes] = await Promise.all([
        fetch('/wp-json/me/v1/menu'),
        fetch('/wp-json/me/v1/settings')
      ]);
      menuItems = await mRes.json();
      settings = await sRes.json();
    } catch (e) {
      console.error(e);
    }
  });
</script>

<footer class="border-t bg-muted/50">
  <div class="container py-12">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
      <div class="col-span-1 md:col-span-2">
        <h3 class="text-lg font-bold mb-4">{settings?.logo_text || "Minimal Engineer"}</h3>
        <p class="text-sm text-muted-foreground">© {currentYear} {settings?.logo_text || "Minimal Engineer"}.</p>
      </div>

      <div>
        <h4 class="text-sm font-semibold mb-4">Navigation</h4>
        <ul class="space-y-3 text-sm text-muted-foreground">
          <li><a href="/" class="hover:text-primary transition-colors">Home</a></li>
          {#each menuItems as item}
            <li>
              <a href={item.url} class="hover:text-primary transition-colors font-medium text-foreground">{item.title}</a>
              {#if item.children && item.children.length > 0}
                <ul class="pl-4 mt-2 space-y-1 border-l">
                  {#each item.children as child}
                    <li><a href={child.url} class="hover:text-primary transition-colors">{child.title}</a></li>
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
            {#if url && typeof url === 'string'}<li><a href={url} target="_blank" rel="noopener" class="hover:text-primary transition-colors capitalize">{name}</a></li>{/if}
          {/each}
        </ul>
      </div>
    </div>
  </div>
</footer>
