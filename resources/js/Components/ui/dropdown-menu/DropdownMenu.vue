<script setup>
import { DropdownMenuRoot, useForwardPropsEmits } from "reka-ui";

const props = defineProps({
  defaultOpen: { type: Boolean, required: false, default: undefined },
  open: { type: Boolean, required: false, default: undefined },
  dir: { type: String, required: false, default: undefined },
  modal: { type: Boolean, required: false, default: true },
});
const emits = defineEmits(["update:open"]);

const forwarded = useForwardPropsEmits(props, emits);
</script>

<template>
  <DropdownMenuRoot v-bind="forwarded">
    <slot />
  </DropdownMenuRoot>
</template>
