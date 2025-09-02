<script setup>
import { AvatarFallback } from "reka-ui";

const props = defineProps({
  delayMs: { type: Number, required: false, default: undefined },
  asChild: { type: Boolean, required: false },
  as: { type: [String, Object, Function], required: false, default: undefined },
});
</script>

<template>
  <AvatarFallback v-bind="props">
    <slot />
  </AvatarFallback>
</template>
