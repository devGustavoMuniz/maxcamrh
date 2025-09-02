<script setup>
import { CollapsibleRoot, useForwardPropsEmits } from "reka-ui";

const props = defineProps({
  defaultOpen: { type: Boolean, required: false, default: undefined },
  open: { type: Boolean, required: false, default: undefined },
  disabled: { type: Boolean, required: false, default: false },
  unmountOnHide: { type: Boolean, required: false, default: false },
  asChild: { type: Boolean, required: false, default: false },
  as: { type: [String, Object, Function], required: false, default: undefined },
});
const emits = defineEmits(["update:open"]);

const forwarded = useForwardPropsEmits(props, emits);
</script>

<template>
  <CollapsibleRoot v-slot="{ open }" v-bind="forwarded">
    <slot :open="open" />
  </CollapsibleRoot>
</template>
