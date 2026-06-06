<script lang="ts">
  import { onMount } from 'svelte';
  import { Button } from './ui/button';
  import { Send } from '@lucide/svelte';
  import { t, type Language } from '$lib/i18n';

  let name = $state("");
  let email = $state("");
  let subject = $state("");
  let message = $state("");
  let status = $state<"idle" | "submitting" | "success" | "error">("idle");
  let settings = $state<any>(null);

  onMount(async () => {
    try {
      const res = await fetch('/wp-json/me/v1/settings');
      settings = await res.json();
    } catch (e) {}
  });

  async function handleSubmit() {
    status = "submitting";
    try {
      const nonce = (window as any).wpData?.nonce || "";
      const res = await fetch('/wp-json/me/v1/contact', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': nonce
        },
        body: JSON.stringify({ name, email, subject, message })
      });
      const data = await res.json();
      if (data.success) {
        status = "success";
        name = email = subject = message = "";
      } else {
        status = "error";
      }
    } catch (e) {
      status = "error";
    }
  }

  let lang = $derived(settings?.language as Language || 'en');
</script>

<div class="max-w-2xl mx-auto py-10 px-6 bg-card border rounded-2xl shadow-sm">
  <h2 class="text-3xl font-bold mb-6">{t('contact', lang)}</h2>
  <form onsubmit={(e) => { e.preventDefault(); handleSubmit(); }} class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="space-y-2">
        <label for="name" class="text-sm font-medium">{t('name', lang)}</label>
        <input id="name" type="text" bind:value={name} required class="w-full px-4 py-2 rounded-md border bg-background focus:ring-2 focus:ring-primary outline-none" />
      </div>
      <div class="space-y-2">
        <label for="email" class="text-sm font-medium">{t('email', lang)}</label>
        <input id="email" type="email" bind:value={email} required class="w-full px-4 py-2 rounded-md border bg-background focus:ring-2 focus:ring-primary outline-none" />
      </div>
    </div>
    <div class="space-y-2">
      <label for="subject" class="text-sm font-medium">{t('subject', lang)}</label>
      <input id="subject" type="text" bind:value={subject} required class="w-full px-4 py-2 rounded-md border bg-background focus:ring-2 focus:ring-primary outline-none" />
    </div>
    <div class="space-y-2">
      <label for="message" class="text-sm font-medium">{t('message', lang)}</label>
      <textarea id="message" bind:value={message} required rows="6" class="w-full px-4 py-2 rounded-md border bg-background focus:ring-2 focus:ring-primary outline-none resize-none"></textarea>
    </div>
    <div class="flex items-center justify-between">
      <p class="text-xs text-muted-foreground italic">{t('protected_by', lang)}</p>
      <Button type="submit" disabled={status === 'submitting'}>
        {#if status === 'submitting'}{t('sending', lang)}{:else}<Send class="h-4 w-4 mr-2" /> {t('send_message', lang)}{/if}
      </Button>
    </div>
    {#if status === 'success'}<div class="p-4 bg-emerald-500/10 text-emerald-500 rounded-md border border-emerald-500/20">{t('contact_thanks', lang)}</div>{/if}
    {#if status === 'error'}<div class="p-4 bg-destructive/10 text-destructive rounded-md border border-destructive/20">Failed to send message. Nonce validation failed or mail server error.</div>{/if}
  </form>
</div>
