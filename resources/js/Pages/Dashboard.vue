<script setup>
import { defineProps } from 'vue';
import { Button } from '@/components/ui/button';

defineProps({
  message: String,
});

const handleButtonClick = () => {
  alert('Botão Shadcn Vue clicado com sucesso!');
};
</script>

<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-8">
    <div class="container mx-auto p-4 sm:p-6 lg:p-8">
      <header class="mb-8 text-center">
        <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-5xl md:text-6xl">
          Dashboard Interativo
        </h1>
      </header>

      <div class="max-w-2xl mx-auto p-6 bg-white dark:bg-gray-800 shadow-xl rounded-lg">
        <p class="text-lg text-gray-700 dark:text-gray-300 mb-6">{{ message }}</p>

        <div class="flex justify-center">
          <Button variant="default" size="lg" @click="handleButtonClick">
            Clique em Mim (Shadcn Vue)
          </Button>
        </div>

        <div class="mt-8 text-center">
          <p class="text-sm text-gray-500 dark:text-gray-400">
            Este botão demonstra a integração do Shadcn Vue.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
