<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";

const props = defineProps({
  franchise_data: Object,
});
</script>

<template>
  <Head
    :title="
      'Detalhes do Franqueado: ' +
      (franchise_data.user ? franchise_data.user.name : franchise_data.cnpj)
    "
  />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2
          class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
        >
          Detalhes do Franqueado:
          {{
            franchise_data.user ? franchise_data.user.name : franchise_data.cnpj
          }}
        </h2>
        <div>
          <Link
            :href="route('franchises.edit', franchise_data.id)"
            class="mr-2"
          >
            <Button variant="outline">Editar</Button>
          </Link>
          <Link :href="route('franchises.index')">
            <Button variant="default">Voltar para a Lista</Button>
          </Link>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
        >
          <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3
              class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100"
            >
              Informações do Franqueado
            </h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-6">
              <div>
                <dt
                  class="text-sm font-medium text-gray-500 dark:text-gray-400"
                >
                  ID do Franqueado
                </dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ franchise_data.id }}
                </dd>
              </div>
              <div>
                <dt
                  class="text-sm font-medium text-gray-500 dark:text-gray-400"
                >
                  CNPJ
                </dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ franchise_data.cnpj }}
                </dd>
              </div>
              <div>
                <dt
                  class="text-sm font-medium text-gray-500 dark:text-gray-400"
                >
                  Email MaxCam
                </dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ franchise_data.maxcam_email }}
                </dd>
              </div>
              <div>
                <dt
                  class="text-sm font-medium text-gray-500 dark:text-gray-400"
                >
                  Máximo de Clientes
                </dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ franchise_data.max_client }}
                </dd>
              </div>
              <div>
                <dt
                  class="text-sm font-medium text-gray-500 dark:text-gray-400"
                >
                  Data Início do Contrato
                </dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ franchise_data.contract_start_date }}
                </dd>
              </div>
              <div>
                <dt
                  class="text-sm font-medium text-gray-500 dark:text-gray-400"
                >
                  Região de Atuação
                </dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ franchise_data.actuation_region }}
                </dd>
              </div>
              <div>
                <dt
                  class="text-sm font-medium text-gray-500 dark:text-gray-400"
                >
                  Documento
                </dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  <a
                    v-if="franchise_data.document_full_url"
                    :href="franchise_data.document_full_url"
                    target="_blank"
                    class="text-blue-600 hover:underline"
                    >Ver Documento</a
                  >
                  <span v-else>Nenhum documento</span>
                </dd>
              </div>
              <div>
                <dt
                  class="text-sm font-medium text-gray-500 dark:text-gray-400"
                >
                  Observações
                </dt>
                <dd
                  class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap"
                >
                  {{ franchise_data.observations || "N/A" }}
                </dd>
              </div>
              <div>
                <dt
                  class="text-sm font-medium text-gray-500 dark:text-gray-400"
                >
                  Franqueado Desde
                </dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ franchise_data.created_at }}
                </dd>
              </div>
              <div>
                <dt
                  class="text-sm font-medium text-gray-500 dark:text-gray-400"
                >
                  Última Atualização (Franquia)
                </dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ franchise_data.updated_at }}
                </dd>
              </div>
            </dl>

            <div
              v-if="franchise_data.user"
              class="pt-6 border-t border-gray-200 dark:border-gray-700"
            >
              <h3
                class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100"
              >
                Informações do Usuário Associado
              </h3>
              <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                  <dt
                    class="text-sm font-medium text-gray-500 dark:text-gray-400"
                  >
                    ID do Usuário
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    {{ franchise_data.user.id }}
                  </dd>
                </div>
                <div>
                  <dt
                    class="text-sm font-medium text-gray-500 dark:text-gray-400"
                  >
                    Nome do Usuário
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    {{ franchise_data.user.name }}
                  </dd>
                </div>
                <div>
                  <dt
                    class="text-sm font-medium text-gray-500 dark:text-gray-400"
                  >
                    Email do Usuário
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    {{ franchise_data.user.email }}
                  </dd>
                </div>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
