<script setup>
import { reactiveOmit } from "@vueuse/core";
import { PopoverContent, PopoverPortal, useForwardPropsEmits } from "reka-ui";
import { cn } from "@/lib/utils";

defineOptions({
  inheritAttrs: false,
});

const props = defineProps({
  forceMount: { type: Boolean, required: false, default: false },
  side: { type: null, required: false, default: "bottom" },
  sideOffset: { type: Number, required: false, default: 4 },
  align: { type: null, required: false, default: "center" },
  alignOffset: { type: Number, required: false, default: 0 },
  avoidCollisions: { type: Boolean, required: false, default: true },
  collisionBoundary: { type: null, required: false, default: undefined },
  collisionPadding: { type: [Number, Object], required: false, default: 0 },
  arrowPadding: { type: Number, required: false, default: 0 },
  sticky: { type: String, required: false, default: "partial" },
  hideWhenDetached: { type: Boolean, required: false, default: false },
  positionStrategy: { type: String, required: false, default: "absolute" },
  updatePositionStrategy: { type: String, required: false, default: "optimized" },
  disableUpdateOnLayoutShift: { type: Boolean, required: false, default: false },
  prioritizePosition: { type: Boolean, required: false, default: false },
  reference: { type: null, required: false, default: undefined },
  asChild: { type: Boolean, required: false, default: false },
  as: { type: [String, Object, Function], required: false, default: undefined },
  disableOutsidePointerEvents: { type: Boolean, required: false, default: false },
  class: { type: null, required: false, default: "" },
});
const emits = defineEmits([
  "escapeKeyDown",
  "pointerDownOutside",
  "focusOutside",
  "interactOutside",
  "openAutoFocus",
  "closeAutoFocus",
]);

const delegatedProps = reactiveOmit(props, "class");

const forwarded = useForwardPropsEmits(delegatedProps, emits);
</script>

<template>
  <PopoverPortal>
    <PopoverContent
      v-bind="{ ...forwarded, ...$attrs }"
      :class="
        cn(
          'z-50 w-72 rounded-md border bg-popover p-4 text-popover-foreground shadow-md outline-none data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2',
          props.class,
        )
      "
    >
      <slot />
    </PopoverContent>
  </PopoverPortal>
</template>
