<script setup>
import { AvatarImage } from "reka-ui";

const props = defineProps({
  src: { type: String, required: true },
  referrerPolicy: { type: null, required: false, default: undefined },
  crossOrigin: { type: null, required: false, default: undefined },
  asChild: { type: Boolean, required: false },
  as: { type: [String, Object, Function], required: false, default: undefined },
});
</script>

<template>
  <AvatarImage v-bind="props" class="h-full w-full object-cover">
    <slot />
  </AvatarImage>
</template>
