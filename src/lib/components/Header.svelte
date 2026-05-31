<script lang="ts">
  import { onMount } from 'svelte';
  import { Moon, Sun, Menu, X, ChevronDown, ChevronRight } from '@lucide/svelte';
  import { Button } from './ui/button';
  import { cn } from '$lib/utils';

  let isDark = false;
  let isMobileMenuOpen = false;
  let pages: any[] = [];
  let settings: any = { logo_text: 'Minimal Engineer' };
  let openMenus: Record<number, boolean> = {};

  onMount(async () => {
    isDark = document.documentElement.classList.contains('dark') ||
             localStorage.getItem('theme') === 'dark' ||
             (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);

    if (isDark) document.documentElement.classList.add('dark');

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

  function toggleTheme() {
    isDark = !isDark;
    document.documentElement.classList.toggle('dark', isDark);
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
  }

  $: hierarchicalPages = pages.filter(p => p.parent === 0).map(parent => ({
    ...parent,
    children: pages.filter(child => child.parent === parent.id)
  }));

  function toggleMenu(id: number) {
    openMenus[id] = !openMenus[id];
    openMenus = { ...openMenus };
  }
</script>

<header class="sticky top-0 z-40 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
  <div class="container flex h-16 items-center justify-between">
    <div class="flex items-center gap-4">
      <a href="/" class="text-xl font-bold">{settings?.logo_text || 'Minimal Engineer'}</a>

      <nav class="hidden md:flex items-center gap-6 ml-6 text-sm font-medium">
        <a href="/" class="transition-colors hover:text-primary">Home</a>
        {#each hierarchicalPages as page}
          <div class="relative group">
            <div class="flex items-center gap-1 cursor-pointer transition-colors hover:text-primary">
              <a href="/{page.slug}">{page.title.rendered}</a>
              {#if page.children.length > 0}
                <ChevronDown class="h-3 w-3" />
              {/if}
            </div>

            {#if page.children.length > 0}
              <div class="absolute left-0 mt-2 w-48 bg-background border rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all">
                <div class="py-1">
                  {#each page.children as child}
                    <a href="/{child.slug}" class="block px-4 py-2 hover:bg-muted transition-colors">{child.title.rendered}</a>
                  {/each}
                </div>
              </div>
            {/if}
          </div>
        {/each}
        <a href="/blog" class="transition-colors hover:text-primary">Blog</a>
      </nav>
    </div>

    <div class="flex items-center gap-2">
      <Button variant="ghost" size="icon" on:click={toggleTheme}>
        {#if isDark}<Sun class="h-5 w-5" />{:else}<Moon class="h-5 w-5" />{/if}
      </Button>
      <Button variant="ghost" size="icon" class="md:hidden" on:click={() => isMobileMenuOpen = !isMobileMenuOpen}>
        {#if isMobileMenuOpen}<X class="h-5 w-5" />{:else}<Menu class="h-5 w-5" />{/if}
      </Button>
    </div>
  </div>
</header>

{#if isMobileMenuOpen}
  <div class="fixed inset-0 z-50 bg-background md:hidden pt-20 px-6 overflow-y-auto">
    <nav class="flex flex-col gap-4 text-lg font-medium pb-20">
      <a href="/" on:click={() => isMobileMenuOpen = false}>Home</a>
      {#each hierarchicalPages as page}
        <div>
          <div class="flex items-center justify-between">
            <a href="/{page.slug}" on:click={() => isMobileMenuOpen = false}>{page.title.rendered}</a>
            {#if page.children.length > 0}
              <Button variant="ghost" size="icon" on:click={() => toggleMenu(page.id)}>
                <ChevronRight class={cn("h-5 w-5 transition-transform", openMenus[page.id] && "rotate-90")} />
              </Button>
            {/if}
          </div>
          {#if page.children.length > 0 && openMenus[page.id]}
            <div class="pl-4 mt-2 flex flex-col gap-2 border-l ml-2">
              {#each page.children as child}
                <a href="/{child.slug}" class="text-base text-muted-foreground" on:click={() => isMobileMenuOpen = false}>{child.title.rendered}</a>
              {/each}
            </div>
          {/if}
        </div>
      {/each}
      <a href="/blog" on:click={() => isMobileMenuOpen = false}>Blog</a>
      <a href="/contact" on:click={() => isMobileMenuOpen = false}>Contact</a>
    </nav>
  </div>
{/if}
