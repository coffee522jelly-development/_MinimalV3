<script lang="ts">
  import { Plus, ArrowUp, LayoutGrid, Folder, User, Mail } from 'lucide-svelte';
  import { Button } from './ui/button';
  import { cn } from '$lib/utils';

  let isOpen = false;

  const actions = [
    { icon: ArrowUp, label: 'Top', href: '#top' },
    { icon: LayoutGrid, label: 'Apps', href: '/apps' },
    { icon: Folder, label: 'Projects', href: '/projects' },
    { icon: User, label: 'About', href: '/about' },
    { icon: Mail, label: 'Contact', href: '/contact' },
  ];

  function toggle() {
    isOpen = !isOpen;
  }

  function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
    isOpen = false;
  }
</script>

<div class="fixed bottom-6 right-6 z-50 flex flex-col-reverse items-center gap-4">
  <Button
    variant="default"
    size="icon"
    class="h-14 w-14 rounded-full shadow-lg"
    on:click={toggle}
  >
    <Plus class={cn("h-6 w-6 transition-transform duration-200", isOpen && "rotate-45")} />
  </Button>

  {#if isOpen}
    <div class="flex flex-col items-center gap-3 animate-in fade-in slide-in-from-bottom-4">
      {#each actions as action}
        <div class="flex items-center gap-2 group">
          <span class="bg-background border px-2 py-1 rounded text-xs font-medium shadow-sm opacity-0 group-hover:opacity-100 transition-opacity">
            {action.label}
          </span>
          {#if action.label === 'Top'}
            <Button
              variant="secondary"
              size="icon"
              class="h-12 w-12 rounded-full shadow-md"
              on:click={scrollToTop}
            >
              <action.icon class="h-5 w-5" />
            </Button>
          {:else}
            <a href={action.href}>
              <Button
                variant="secondary"
                size="icon"
                class="h-12 w-12 rounded-full shadow-md"
              >
                <action.icon class="h-5 w-5" />
              </Button>
            </a>
          {/if}
        </div>
      {/each}
    </div>
  {/if}
</div>
