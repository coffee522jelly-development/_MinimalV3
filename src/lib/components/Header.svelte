<script lang="ts">
  import { onMount } from 'svelte';
  import { Moon, Sun, Menu, X, ChevronDown, ChevronRight } from '@lucide/svelte';
  import { Button } from './ui/button';
  import { cn } from '$lib/utils';
  import { t, type Language } from '$lib/i18n';
  import { getRestUrl, fetchWithCache } from '$lib/api';

  let isDark = $state(false);
  let isMobileMenuOpen = $state(false);
  let menuItems = $state<any[]>([]);
  let settings = $state<any>({ logo_text: window.wpData?.siteName || 'Minimal Engineer', language: 'en' });
  let openMenus = $state<Record<number, boolean>>({});

  onMount(async () => {
    isDark = document.documentElement.classList.contains('dark') ||
             localStorage.getItem('theme') === 'dark' ||
             (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);

    if (isDark) document.documentElement.classList.add('dark');

    try {
      const [mItems, sData] = await Promise.all([
        fetchWithCache(getRestUrl('me/v1/menu')),
        fetchWithCache(getRestUrl('me/v1/settings'))
      ]);
      menuItems = mItems;
      settings = sData;
    } catch (e) {
      console.error(e);
    }
  });

  function toggleTheme() {
    isDark = !isDark;
    document.documentElement.classList.toggle('dark', isDark);
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
  }

  let hierarchicalPages = $derived(menuItems);
  let lang = $derived(settings?.language as Language || 'en');

  function toggleMenu(id: number) {
    openMenus[id] = !openMenus[id];
  }
</script>

<header class="sticky top-0 z-40 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
  <div class="container flex h-16 items-center justify-between">
    <div class="flex items-center gap-4">
      <a href="/" class="text-xl font-bold">{settings?.logo_text || window.wpData?.siteName || 'Minimal Engineer'}</a>

      <nav class="hidden md:flex items-center gap-6 ml-6 text-sm font-medium">
        <a href="/" class="transition-colors hover:text-primary">{t('home', lang)}</a>
        {#each hierarchicalPages as item}
          <div class="relative group">
            <div class="flex items-center gap-1 cursor-pointer transition-colors hover:text-primary">
              <a href={item.url}>{item.title}</a>
              {#if item.children && item.children.length > 0}
                <ChevronDown class="h-3 w-3" />
              {/if}
            </div>

            {#if item.children && item.children.length > 0}
              <div class="absolute left-0 mt-2 w-48 bg-background border rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all">
                <div class="py-1">
                  {#each item.children as child}
                    <a href={child.url} class="block px-4 py-2 hover:bg-muted transition-colors">{child.title}</a>
                  {/each}
                </div>
              </div>
            {/if}
          </div>
        {/each}
      </nav>
    </div>

    <div class="flex items-center gap-2">
      <Button variant="ghost" size="icon" onclick={toggleTheme}>
        {#if isDark}<Sun class="h-5 w-5" />{:else}<Moon class="h-5 w-5" />{/if}
      </Button>
      <Button variant="ghost" size="icon" class="md:hidden" onclick={() => isMobileMenuOpen = !isMobileMenuOpen}>
        {#if isMobileMenuOpen}<X class="h-5 w-5" />{:else}<Menu class="h-5 w-5" />{/if}
      </Button>
    </div>
  </div>
</header>

{#if isMobileMenuOpen}
  <div class="fixed inset-0 z-50 bg-background md:hidden pt-20 px-6 overflow-y-auto">
    <nav class="flex flex-col gap-4 text-lg font-medium pb-20">
      <a href="/" onclick={() => isMobileMenuOpen = false}>{t('home', lang)}</a>
      {#each hierarchicalPages as item}
        <div>
          <div class="flex items-center justify-between">
            <a href={item.url} onclick={() => isMobileMenuOpen = false}>{item.title}</a>
            {#if item.children && item.children.length > 0}
              <Button variant="ghost" size="icon" onclick={() => toggleMenu(item.id)}>
                <ChevronRight class={cn("h-5 w-5 transition-transform", openMenus[item.id] && "rotate-90")} />
              </Button>
            {/if}
          </div>
          {#if item.children && item.children.length > 0 && openMenus[item.id]}
            <div class="pl-4 mt-2 flex flex-col gap-2 border-l ml-2">
              {#each item.children as child}
                <a href={child.url} class="text-base text-muted-foreground" onclick={() => isMobileMenuOpen = false}>{child.title}</a>
              {/each}
            </div>
          {/if}
        </div>
      {/each}
      <a href="/contact" onclick={() => isMobileMenuOpen = false}>{t('contact', lang)}</a>
    </nav>
  </div>
{/if}
