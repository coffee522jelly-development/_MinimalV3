<script lang="ts">
  import { onMount } from 'svelte';
  import { cn } from '$lib/utils';

  export let content: string;

  let headings: { id: string, text: string, level: number }[] = [];
  let activeId = "";

  onMount(() => {
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = content;
    const headingElements = tempDiv.querySelectorAll('h2, h3, h4');

    headings = Array.from(headingElements).map((el, i) => {
      const id = el.id || `heading-${i}`;
      return {
        id,
        text: el.textContent || "",
        level: parseInt(el.tagName.substring(1))
      };
    });

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          activeId = entry.target.id;
        }
      });
    }, { rootMargin: '0px 0px -80% 0px' });
  });
</script>

{#if headings.length > 0}
  <nav class="space-y-2 text-sm">
    <p class="font-bold mb-4">Table of Contents</p>
    <ul class="space-y-2 border-l-2 ml-1">
      {#each headings as heading}
        <li
          class={cn(
            "pl-4 transition-colors hover:text-primary",
            activeId === heading.id ? "border-l-2 border-primary -ml-[2px] text-primary font-medium" : "text-muted-foreground",
            heading.level === 3 && "ml-4",
            heading.level === 4 && "ml-8"
          )}
        >
          <a href="#{heading.id}">{heading.text}</a>
        </li>
      {/each}
    </ul>
  </nav>
{/if}
