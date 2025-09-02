<script setup>
import { Link } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";

const props = defineProps({
    currentStep: {
        type: Number,
        required: true,
    },
    totalSteps: {
        type: Number,
        required: true,
    },
    formProcessing: {
        type: Boolean,
        default: false,
    },
    submitButtonText: {
        type: String,
        default: "Salvar Colaborador",
    },
    cancelRoute: {
        type: String,
        default: "collaborators.index",
    },
});

const emit = defineEmits(["next", "prev", "submit"]);

const nextStep = () => {
    emit("next");
};

const prevStep = () => {
    emit("prev");
};

const submitForm = () => {
    emit("submit");
};
</script>

<template>
    <div
        class="flex flex-col sm:flex-row justify-between items-center mt-8 pt-6 border-t dark:border-gray-700 gap-4"
    >
        <div>
            <Button
                v-if="currentStep > 1"
                type="button"
                variant="outline"
                class="bg-white dark:bg-gray-700 w-full sm:w-auto"
                @click="prevStep"
            >
                Anterior
            </Button>
        </div>
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
            <Link
                :href="route(cancelRoute)"
                class="w-full sm:w-auto"
            >
                <Button
                    variant="outline"
                    class="bg-white dark:bg-gray-700 w-full"
                    type="button"
                >
                    Cancelar
                </Button>
            </Link>
            <Button
                v-if="currentStep < totalSteps"
                type="button"
                class="bg-gray-500 text-white hover:bg-gray-400 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500 w-full sm:w-auto"
                @click="nextStep"
            >
                Próximo
            </Button>
            <Button
                type="submit"
                variant="black"
                class="bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 w-full sm:w-auto"
                :disabled="formProcessing"
                @click="submitForm"
            >
                {{ submitButtonText }}
            </Button>
        </div>
    </div>
</template>