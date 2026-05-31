<script lang="ts">
  import { onMount } from 'svelte';
  import { Moon, Sun, Menu, X } from 'lucide-svelte';
  import { Button } from './ui/button';

  let isDark = false;
  let isMobileMenuOpen = false;
  let pages: any[] = [];
  let settings: any = { logo_text: 'Minimal Engineer' };

  onMount(async () => {
    isDark = document.documentElement.classList.contains('dark') ||
             localStorage.getItem('theme') === 'dark' ||
             (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);

    if (isDark) {
      document.documentElement.classList.add('dark');
    }

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

  function toggleTheme() {
    isDark = !isDark;
    if (isDark) {
      document.documentElement.classList.add('dark');
      localStorage.setItem('theme', 'dark');
    } else {
      document.documentElement.classList.remove('dark');
      localStorage.setItem('theme', 'light');
    }
  }

  function toggleMobileMenu() {
    isMobileMenuOpen = !isMobileMenuOpen;
  }
</script>

<header class="sticky top-0 z-40 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
  <div class="container flex h-16 items-center justify-between">
    <div class="flex items-center gap-4">
      <a href="/" class="text-xl font-bold">{settings?.logo_text || 'Minimal Engineer'}</a>

      <nav class="hidden md:flex items-center gap-6 ml-6 text-sm font-medium">
        <a href="/" class="transition-colors hover:text-primary">Home</a>
        {#each pages as page}
          <a href="/{page.slug}" class="transition-colors hover:text-primary">{page.title.rendered}</a>
        {/each}
        <a href="/blog" class="transition-colors hover:text-primary">Blog</a>
      </nav>
    </div>

    <div class="flex items-center gap-2">
      <Button variant="ghost" size="icon" on:click={toggleTheme} aria-label="Toggle theme">
        {#if isDark}
          <Sun class="h-5 w-5" />
        {:else}
          <Moon class="h-5 w-5" />
        {/if}
      </Button>

      <Button variant="ghost" size="icon" class="md:hidden" on:click={toggleMobileMenu} aria-label="Toggle menu">
        {#if isMobileMenuOpen}
          <X class="h-5 w-5" />
        {:else}
          <Menu class="h-5 w-5" />
        {/if}
      </Button>
    </div>
  </div>
</header>

{#if isMobileMenuOpen}
  <div class="fixed inset-0 z-50 bg-background md:hidden pt-20 px-6">
    <nav class="flex flex-col gap-6 text-lg font-medium">
      <a href="/" on:click={toggleMobileMenu}>Home</a>
      {#each pages as page}
        <a href="/{page.slug}" on:click={toggleMobileMenu}>{page.title.rendered}</a>
      {/each}
      <a href="/blog" on:click={toggleMobileMenu}>Blog</a>
      <a href="/contact" on:click={toggleMobileMenu}>Contact</a>
    </nav>
  </div>
{/if}
