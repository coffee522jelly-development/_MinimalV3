<script lang="ts">
  import { cn } from "$lib/utils";
  import { buttonVariants, type Variant, type Size } from "./index.js";

  let {
    class: className = undefined,
    variant = "default",
    size = "default",
    type = "button",
    children,
    onclick,
    ...rest
  } = $props<{
    class?: string;
    variant?: Variant;
    size?: Size;
    type?: "button" | "submit" | "reset";
    children?: import('svelte').Snippet;
    onclick?: (e: MouseEvent) => void;
    [key: string]: any;
  }>();
</script>

<button
  {type}
  class={cn(buttonVariants({ variant, size, className }))}
  {onclick}
  {...rest}
>
  {@render children?.()}
</button>
