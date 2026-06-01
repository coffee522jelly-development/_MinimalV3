<script lang="ts">
  import { onMount, tick } from 'svelte';
  import { cn } from '$lib/utils';

  let { content, label = "Table of Contents" } = $props<{ content: string, label?: string }>();

  let headings = $state<{ id: string, text: string, level: number }[]>([]);
  let activeId = $state("");

  onMount(() => {
    updateHeadings();
  });

  $effect(() => {
    if (content) {
      tick().then(updateHeadings);
    }
  });

  function updateHeadings() {
    const headingElements = document.querySelectorAll('article .prose h2, article .prose h3, article .prose h4');

    headings = Array.from(headingElements).map((el, i) => {
      if (!el.id) el.id = `heading-${i}`;
      return {
        id: el.id,
        text: el.textContent || "",
        level: parseInt(el.tagName.substring(1))
      };
    });

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) activeId = entry.target.id;
      });
    }, { rootMargin: '-10% 0px -80% 0px' });

    headingElements.forEach(el => observer.observe(el));
    return () => observer.disconnect();
  }

  function scrollToHeading(e: MouseEvent, id: string) {
    e.preventDefault();
    const el = document.getElementById(id);
    if (el) {
      window.scrollTo({ top: el.offsetTop - 100, behavior: 'smooth' });
      history.pushState(null, '', `#${id}`);
    }
  }
</script>

{#if headings.length > 0}
  <nav class="space-y-2 text-sm">
    <p class="font-bold mb-4 uppercase tracking-wider text-xs text-muted-foreground">{label || 'Table of Contents'}</p>
    <ul class="space-y-2 border-l ml-1">
      {#each headings as heading}
        <li
          class={cn(
            "pl-4 transition-colors hover:text-primary leading-snug",
            activeId === heading.id ? "border-l-2 border-primary -ml-[1px] text-primary font-bold" : "text-muted-foreground",
            heading.level === 3 && "ml-4",
            heading.level === 4 && "ml-8"
          )}
        >
          <a href="#{heading.id}" onclick={(e) => scrollToHeading(e, heading.id)}>{heading.text}</a>
        </li>
      {/each}
    </ul>
  </nav>
{/if}
