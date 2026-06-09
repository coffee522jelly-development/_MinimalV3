<script lang="ts">
  import { onMount } from 'svelte';
  import { cn } from '$lib/utils';

  let {
    src,
    alt = "",
    class: className = "",
    placeholder = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII="
  } = $props<{
    src: string;
    alt?: string;
    class?: string;
    placeholder?: string;
  }>();

  let loaded = $state(false);
  let visible = $state(false);
  let containerEl = $state<HTMLElement | null>(null);

  onMount(() => {
    const observer = new IntersectionObserver((entries) => {
      if (entries[0].isIntersecting) {
        visible = true;
        observer.disconnect();
      }
    }, { rootMargin: '200px' });

    if (containerEl) observer.observe(containerEl);
    return () => observer.disconnect();
  });

  function handleLoad() {
    loaded = true;
  }
</script>

<div
  bind:this={containerEl}
  class={cn("relative overflow-hidden bg-muted", className)}
>
  {#if !loaded}
    <div class="absolute inset-0 animate-pulse bg-muted-foreground/10"></div>
  {/if}

  {#if visible}
    <img
      {src}
      {alt}
      class={cn(
        "h-full w-full object-cover transition-opacity duration-500",
        loaded ? "opacity-100" : "opacity-0"
      )}
      loading="lazy"
      onload={handleLoad}
    />
  {/if}
</div>
