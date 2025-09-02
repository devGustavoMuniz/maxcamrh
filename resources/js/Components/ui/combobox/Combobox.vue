<script setup>
import { ComboboxRoot, useForwardPropsEmits } from "reka-ui";

const props = defineProps({
  open: { type: Boolean, required: false, default: undefined },
  defaultOpen: { type: Boolean, required: false, default: undefined },
  resetSearchTermOnBlur: { type: Boolean, required: false, default: false },
  resetSearchTermOnSelect: { type: Boolean, required: false, default: false },
  ignoreFilter: { type: Boolean, required: false, default: false },
  modelValue: { type: null, required: false, default: undefined },
  defaultValue: { type: null, required: false, default: undefined },
  multiple: { type: Boolean, required: false, default: false },
  dir: { type: String, required: false, default: undefined },
  disabled: { type: Boolean, required: false, default: false },
  highlightOnHover: { type: Boolean, required: false, default: false },
  by: { type: [String, Function], required: false, default: undefined },
  asChild: { type: Boolean, required: false, default: false },
  as: { type: [String, Object, Function], required: false, default: undefined },
  name: { type: String, required: false, default: undefined },
  required: { type: Boolean, required: false, default: false },
});
const emits = defineEmits(["update:modelValue", "highlight", "update:open"]);

const forwarded = useForwardPropsEmits(props, emits);
</script>

<template>
  <ComboboxRoot v-bind="forwarded">
    <slot />
  </ComboboxRoot>
</template>
