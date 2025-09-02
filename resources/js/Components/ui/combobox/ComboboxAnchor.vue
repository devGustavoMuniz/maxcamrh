<script setup>
import { reactiveOmit } from "@vueuse/core";
import { ComboboxAnchor, useForwardProps } from "reka-ui";
import { cn } from "@/lib/utils";

const props = defineProps({
  reference: { type: null, required: false, default: undefined },
  asChild: { type: Boolean, required: false, default: false },
  as: { type: [String, Object, Function], required: false, default: undefined },
  class: { type: null, required: false, default: "" },
});

const delegatedProps = reactiveOmit(props, "class");

const forwarded = useForwardProps(delegatedProps);
</script>

<template>
  <ComboboxAnchor v-bind="forwarded" :class="cn('w-[200px]', props.class)">
    <slot />
  </ComboboxAnchor>
</template>
