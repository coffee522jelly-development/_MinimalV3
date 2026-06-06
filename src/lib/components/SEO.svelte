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
  <meta name="description" content={description} />
  <meta property="og:title" content={title} />
  <meta property="og:description" content={description} />
  <meta property="og:type" content={type === 'article' ? 'article' : 'website'} />
  {#if image}<meta property="og:image" content={image} />{/if}
  {#if url}<meta property="og:url" content={url} />{/if}
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content={title} />
  <meta name="twitter:description" content={description} />
  {#if image}<meta name="twitter:image" content={image} />{/if}

  <script type="application/ld+json">
    {JSON.stringify(jsonLd)}
  </script>
</svelte:head>
