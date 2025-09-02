<script setup>
import { DialogClose } from "reka-ui";

const props = defineProps({
  asChild: { type: Boolean, required: false, default: false },
  as: { type: [String, Object, Function], required: false, default: undefined },
});
</script>

<template>
  <DialogClose v-bind="props">
    <slot />
  </DialogClose>
</template>
