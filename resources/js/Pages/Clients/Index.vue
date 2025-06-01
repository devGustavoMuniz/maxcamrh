<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3'; // Adicionado router
import { computed } from 'vue';
import {
  Table, TableBody, TableCell, TableHead, TableHeader, TableRow,
} from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import {
  DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
// import { MoreHorizontal } from 'lucide-vue-next';
import Pagination from '@/components/Pagination.vue';

defineProps({
  clients: Object,
});

const flash = computed(() => usePage().props.flash);

const deleteClient = (clientId) => {
  if (confirm('Tem certeza que deseja excluir este cliente e seu usuário associado?')) {
    router.delete(route('clients.destroy', clientId), {
      preserveScroll: true,
    });
  }
};
</script>

<template>
  <Head title="Clientes" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Gerenciar Clientes
        </h2>
        <Link :href="route('clients.create')">
          <Button variant="default">Adicionar Cliente</Button>
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div v-if="flash && flash.success" class="mb-4 p-4 rounded-md bg-green-50 dark:bg-green-900 text-sm font-medium text-green-800 dark:text-green-200">
          {{ flash.success }}
        </div>
        <div v-if="flash && flash.error" class="mb-4 p-4 rounded-md bg-red-50 dark:bg-red-900 text-sm font-medium text-red-800 dark:text-red-200">
          {{ flash.error }}
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Logo</TableHead>
                <TableHead>Nome (Usuário)</TableHead>
                <TableHead>Email (Usuário)</TableHead>
                <TableHead>CNPJ</TableHead>
                <TableHead>Telefone</TableHead>
                <TableHead>Contrato Mensal</TableHead>
                <TableHead class="text-right">Ações</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-if="clients.data.length === 0">
                <TableCell colspan="6" class="text-center">Nenhum cliente encontrado.</TableCell>
              </TableRow>
              <TableRow v-for="client in clients.data" :key="client.id">
                <TableCell>
                  <img v-if="client.logo_full_url" :src="client.logo_full_url" alt="Logo" class="h-10 w-10 rounded-full object-cover">
                  <span v-else>N/A</span>
                </TableCell>
                <TableCell>{{ client.user_name }}</TableCell>
                <TableCell>{{ client.user_email }}</TableCell>
                <TableCell>{{ client.cnpj }}</TableCell>
                <TableCell>{{ client.phone || 'N/A' }}</TableCell>
                <TableCell>{{ client.is_monthly_contract ? 'Sim' : 'Não' }}</TableCell>
                <TableCell class="text-right">
                  <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                      <Button variant="ghost" class="h-8 w-8 p-0">
                        <span class="sr-only">Abrir menu</span>
                        <span>...</span>
                      </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                      <DropdownMenuItem as-child>
                        <Link :href="route('clients.show', client.id)">Visualizar</Link>
                      </DropdownMenuItem>
                      <DropdownMenuItem as-child>
                        <Link :href="route('clients.edit', client.id)">Editar</Link>
                      </DropdownMenuItem>
                      <DropdownMenuItem @click="deleteClient(client.id)" class="text-red-600">
                        Excluir
                      </DropdownMenuItem>
                    </DropdownMenuContent>
                  </DropdownMenu>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
        <Pagination class="mt-6" :links="clients.links" />
      </div>
    </div>
  </AuthenticatedLayout>
</template>
