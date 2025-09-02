<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const flash = computed(() => usePage().props.flash);
</script>

<template>
    <div
        v-if="flash && flash.success"
        class="mb-4 p-4 rounded-md bg-green-100 dark:bg-green-800 text-sm font-medium text-green-700 dark:text-green-200"
    >
        {{ flash.success }}
    </div>
    <div
        v-if="flash && flash.error"
        class="mb-4 p-4 rounded-md bg-red-100 dark:bg-red-800 text-sm font-medium text-red-700 dark:text-red-200"
    >
        {{ flash.error }}
    </div>
</template>
