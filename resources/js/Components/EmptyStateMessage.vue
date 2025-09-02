<script setup>
const { message } = defineProps({
    message: {
        type: String,
        default: "Nenhum item encontrado.",
    },
});
</script>

<template>
    <div class="text-center py-8 text-gray-500 dark:text-gray-400 px-4">
        {{ message }}
    </div>
</template>
