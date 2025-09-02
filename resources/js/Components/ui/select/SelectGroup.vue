<script setup>
import { reactiveOmit } from "@vueuse/core";
import { SelectGroup } from "reka-ui";
import { cn } from "@/lib/utils";

const props = defineProps({
  asChild: { type: Boolean, required: false, default: false },
  as: { type: [String, Object, Function], required: false, default: undefined },
  class: { type: null, required: false, default: "" },
});

const delegatedProps = reactiveOmit(props, "class");
</script>

<template>
  <SelectGroup :class="cn('p-1 w-full', props.class)" v-bind="delegatedProps">
    <slot />
  </SelectGroup>
</template>
