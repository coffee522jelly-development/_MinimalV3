<script lang="ts">
  import { onMount } from 'svelte';

  let pages: any[] = [];
  let settings: any = null;
  let siteTitle = "Minimal Engineer";
  let currentYear = new Date().getFullYear();

  onMount(async () => {
    try {
      const [pRes, sRes] = await Promise.all([
        fetch('/wp-json/wp/v2/pages?parent=0&orderby=menu_order&order=asc'),
        fetch('/wp-json/me/v1/settings')
      ]);
      pages = await pRes.json();
      settings = await sRes.json();
    } catch (e) {
      console.error('Failed to fetch data', e);
    }
  });

  $: siteTitle = settings?.logo_text || "Minimal Engineer";
</script>

<footer class="border-t bg-muted/50">
  <div class="container py-12">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
      <div class="col-span-1 md:col-span-2">
        <h3 class="text-lg font-bold mb-4">{siteTitle}</h3>
        <p class="text-sm text-muted-foreground">
          © {currentYear} {siteTitle}. All rights reserved.
        </p>
      </div>

      <div>
        <h4 class="text-sm font-semibold mb-4">Pages</h4>
        <ul class="space-y-2 text-sm text-muted-foreground">
          <li><a href="/" class="hover:text-primary transition-colors">Home</a></li>
          {#each pages as page}
            <li><a href="/{page.slug}" class="hover:text-primary transition-colors">{page.title.rendered}</a></li>
          {/each}
        </ul>
      </div>

      <div>
        <h4 class="text-sm font-semibold mb-4">Social</h4>
        <ul class="space-y-2 text-sm text-muted-foreground">
          {#if settings?.sns?.github}
            <li><a href={settings.sns.github} target="_blank" rel="noopener" class="hover:text-primary transition-colors">GitHub</a></li>
          {/if}
          {#if settings?.sns?.x}
            <li><a href={settings.sns.x} target="_blank" rel="noopener" class="hover:text-primary transition-colors">X (Twitter)</a></li>
          {/if}
          {#if settings?.sns?.youtube}
            <li><a href={settings.sns.youtube} target="_blank" rel="noopener" class="hover:text-primary transition-colors">YouTube</a></li>
          {/if}
          {#if settings?.sns?.qiita}
            <li><a href={settings.sns.qiita} target="_blank" rel="noopener" class="hover:text-primary transition-colors">Qiita</a></li>
          {/if}
          {#if settings?.sns?.zenn}
            <li><a href={settings.sns.zenn} target="_blank" rel="noopener" class="hover:text-primary transition-colors">Zenn</a></li>
          {/if}
        </ul>
      </div>
    </div>
  </div>
</footer>
