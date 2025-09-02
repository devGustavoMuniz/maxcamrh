<script setup>
import { DropdownMenuGroup } from "reka-ui";

const props = defineProps({
  asChild: { type: Boolean, required: false, default: false },
  as: { type: [String, Object, Function], required: false, default: undefined },
});
</script>

<template>
  <DropdownMenuGroup v-bind="props">
    <slot />
  </DropdownMenuGroup>
</template>
