<script lang="ts">
  import { Button as ButtonPrimitive } from "bits-ui";
  import { buttonVariants, type Variant, type Size } from "./index.js";
  import { cn } from "$lib/utils";

  let className: string | undefined = undefined;
  export { className as class };
  export let variant: Variant = "default";
  export let size: Size = "default";
  export let builders: any[] = [];
</script>

<ButtonPrimitive.Root
  {builders}
  class={cn(buttonVariants({ variant, size, className }))}
  type="button"
  on:click
  on:keydown
  on:mouseenter
  on:mouseleave
  {...$$restProps}
>
  <slot />
</ButtonPrimitive.Root>
