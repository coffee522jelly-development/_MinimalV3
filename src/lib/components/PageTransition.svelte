<script lang="ts">
  import { fade } from 'svelte/transition';
  import { onMount } from 'svelte';
  import { getRestUrl } from '$lib/api';
  import { useLocation } from 'svelte-routing';

  let { children } = $props<{ children: any }>();
  let location = useLocation();

  let enableTransition = $state(true);
  let isReady = $state(false);

  onMount(async () => {
    try {
      const res = await fetch(getRestUrl('me/v1/settings'));
      const settings = await res.json();
      enableTransition = settings?.enable_page_transition ?? true;
    } catch (e) {
      console.error(e);
    } finally {
      isReady = true;
    }
  });
</script>

{#if isReady}
  {#if enableTransition}
    {#key $location.pathname}
      <div in:fade={{ duration: 300, delay: 150 }} out:fade={{ duration: 150 }}>
        {@render children()}
      </div>
    {/key}
  {:else}
    {@render children()}
  {/if}
{/if}
