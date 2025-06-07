<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

const props = defineProps({
  client_data: Object,
});
</script>

<template>
  <Head :title="'Detalhes do Cliente: ' + (client_data.user ? client_data.user.name : client_data.cnpj)" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Detalhes do Cliente: {{ client_data.user ? client_data.user.name : client_data.cnpj }}
        </h2>
        <div>
          <Link :href="route('clients.edit', client_data.id)" class="mr-2">
            <Button variant="outline">Editar</Button>
          </Link>
          <Link :href="route('clients.index')">
            <Button variant="default">Voltar para a Lista</Button>
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Informações da Empresa Cliente</h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-6">
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Logo</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  <img v-if="client_data.logo_full_url" :src="client_data.logo_full_url" alt="Logo" class="h-24 w-auto rounded shadow my-2">
                  <span v-else>Nenhum logo cadastrado.</span>
                </dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nome do Usuário</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ client_data.user.name }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email do Usuário</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ client_data.user.email }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Usuário Criado em</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ client_data.user.user_created_at }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID do Cliente</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ client_data.id }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">CNPJ</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ client_data.cnpj }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Telefone</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ client_data.phone }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Contrato Mensal</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ client_data.is_monthly_contract }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Data Final do Contrato</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ client_data.contract_end_date }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Número de Teste</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ client_data.test_number }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Cliente Desde</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ client_data.created_at }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Última Atualização (Cliente)</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ client_data.updated_at }}</dd>
              </div>
            </dl>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
