<script setup>
import { Button } from "@/Components/ui/button";

const props = defineProps({
    currentStep: {
        type: Number,
        required: true,
    },
    stepNames: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(["go-to-step"]);

const goToStep = (step) => {
    emit("go-to-step", step);
};
</script>

<template>
    <div class="mb-6 flex flex-wrap justify-center gap-2">
        <Button
            v-for="(name, index) in stepNames"
            :key="index + 1"
            :variant="currentStep === index + 1 ? 'default' : 'outline'"
            :class="{
                'bg-gray-800 text-white hover:bg-gray-700':
                    currentStep === index + 1,
                'bg-white dark:bg-gray-800': currentStep !== index + 1,
            }"
            class="text-xs sm:text-sm"
            @click="goToStep(index + 1)"
        >
            Etapa {{ index + 1 }}: {{ name }}
        </Button>
    </div>
</template>