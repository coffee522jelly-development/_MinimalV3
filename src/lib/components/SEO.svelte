<script lang="ts">
  import { onMount } from 'svelte';

  export let title: string;
  export let description: string = "";
  export let image: string = "";
  export let type: "website" | "article" = "website";
  export let url: string = "";

  const siteName = "Minimal Engineer";

  onMount(() => {
    document.title = title ? `${title} | ${siteName}` : siteName;
  });

  $: jsonLd = {
    "@context": "https://schema.org",
    "@type": type === 'article' ? 'BlogPosting' : 'WebSite',
    "headline": title,
    "description": description,
    "image": image,
    "url": url
  };
</script>

<svelte:head>
  <script type="application/ld+json">
    {JSON.stringify(jsonLd)}
  </script>
</svelte:head>
