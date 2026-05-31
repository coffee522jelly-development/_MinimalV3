<script lang="ts">
  import { Button } from './ui/button';
  import { Send } from '@lucide/svelte';

  let name = "";
  let email = "";
  let subject = "";
  let message = "";
  let status: "idle" | "submitting" | "success" | "error" = "idle";

  async function handleSubmit() {
    status = "submitting";
    setTimeout(() => {
      status = "success";
      name = email = subject = message = "";
    }, 1500);
  }
</script>

<div class="max-w-2xl mx-auto py-10 px-6 bg-card border rounded-2xl shadow-sm">
  <h2 class="text-3xl font-bold mb-6">Contact</h2>
  <form on:submit|preventDefault={handleSubmit} class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="space-y-2">
        <label for="name" class="text-sm font-medium">Name</label>
        <input id="name" type="text" bind:value={name} required class="w-full px-4 py-2 rounded-md border bg-background" />
      </div>
      <div class="space-y-2">
        <label for="email" class="text-sm font-medium">Email</label>
        <input id="email" type="email" bind:value={email} required class="w-full px-4 py-2 rounded-md border bg-background" />
      </div>
    </div>
    <div class="space-y-2">
      <label for="subject" class="text-sm font-medium">Subject</label>
      <input id="subject" type="text" bind:value={subject} required class="w-full px-4 py-2 rounded-md border bg-background" />
    </div>
    <div class="space-y-2">
      <label for="message" class="text-sm font-medium">Message</label>
      <textarea id="message" bind:value={message} required rows="6" class="w-full px-4 py-2 rounded-md border bg-background resize-none"></textarea>
    </div>
    <div class="flex items-center justify-between">
      <p class="text-xs text-muted-foreground italic">Protected by reCAPTCHA</p>
      <Button type="submit" disabled={status === 'submitting'}>
        {#if status === 'submitting'}Sending...{:else}<Send class="h-4 w-4 mr-2" /> Send Message{/if}
      </Button>
    </div>
  </form>
</div>
