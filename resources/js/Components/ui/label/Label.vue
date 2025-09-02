<script setup>
import { reactiveOmit } from "@vueuse/core";
import { Label } from "reka-ui";
import { cn } from "@/lib/utils";

const props = defineProps({
  for: { type: String, required: false, default: undefined },
  asChild: { type: Boolean, required: false, default: false },
  as: { type: [String, Object, Function], required: false, default: undefined },
  class: { type: null, required: false, default: "" },
});

const delegatedProps = reactiveOmit(props, "class");
</script>

<template>
  <Label
    v-bind="delegatedProps"
    :class="
      cn(
        'text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70',
        props.class,
      )
    "
  >
    <slot />
  </Label>
</template>
