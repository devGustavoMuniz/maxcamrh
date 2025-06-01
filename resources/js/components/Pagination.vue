<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  links: Array,
});
</script>

<template>
  <div v-if="links.length > 3">
    <div class="flex flex-wrap -mx-1">
      <template v-for="(link, key) in links" :key="key">
        <div
          v-if="link.url === null"
          class="px-3 py-2 mx-1 text-sm leading-4 text-gray-500 bg-white dark:bg-gray-800 border rounded-md cursor-not-allowed dark:text-gray-400"
          v-html="link.label"
        />
        <Link
          v-else
          class="px-3 py-2 mx-1 text-sm leading-4 border rounded-md focus:outline-none focus:ring focus:ring-opacity-50 transition-colors duration-150"
          :class="{
                        'bg-blue-500 text-white dark:bg-blue-600 dark:text-white border-blue-500 dark:border-blue-600 hover:bg-blue-600 dark:hover:bg-blue-700': link.active,
                        'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-blue-200 dark:focus:ring-blue-400': !link.active
                    }"
          :href="link.url"
          v-html="link.label"
        />
      </template>
    </div>
  </div>
</template>
