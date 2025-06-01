<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button'; // Para os botões de ação

const props = defineProps({
  admin_user: Object,
});
</script>

<template>
  <Head :title="'Detalhes do Administrador: ' + admin_user.name" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Detalhes do Administrador: {{ admin_user.name }}
        </h2>
        <div>
          <Link :href="route('admins.edit', admin_user.id)" class="mr-2">
            <Button variant="outline">Editar</Button>
          </Link>
          <Link :href="route('admins.index')">
            <Button variant="default">Voltar para a Lista</Button>
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ admin_user.id }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nome</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ admin_user.name }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ admin_user.email }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Papel (Role)</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 uppercase">{{ admin_user.role }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Data de Criação</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ admin_user.created_at }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email Verificado em</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ admin_user.email_verified_at }}</dd>
              </div>
            </dl>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
