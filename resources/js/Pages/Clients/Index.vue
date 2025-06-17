<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import debounce from 'lodash/debounce';

// Componentes da UI
import {
  Table, TableBody, TableCell, TableHead, TableHeader, TableRow,
} from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
  CardDescription,
  CardFooter
} from '@/components/ui/card';
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";


// Ícones
import { Plus, FileEdit, Trash2, Search } from 'lucide-vue-next';

// Paginação
import Pagination from '@/components/Pagination.vue';

const props = defineProps({
  clients: Object,
  filters: Object,
});

const flash = computed(() => usePage().props.flash);

// Lógica de Busca
const search = ref(props.filters?.search || '');
watch(search, debounce((value) => {
  router.get(route('clients.index'), { search: value }, {
    preserveState: true,
    replace: true,
    preserveScroll: true,
  });
}, 300));

// Função para deletar
const deleteClient = (clientId) => {
  if (confirm('Tem certeza que deseja excluir este cliente e seu usuário associado? Todos os colaboradores vinculados também podem ser afetados ou excluídos. Esta ação não poderá ser desfeita.')) {
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
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Gerenciar Clientes
      </h2>
    </template>

    <div class="mx-auto w-full">
      <div v-if="flash && flash.success" class="mb-4 p-4 rounded-md bg-green-100 dark:bg-green-800 text-sm font-medium text-green-700 dark:text-green-200">
        {{ flash.success }}
      </div>
      <div v-if="flash && flash.error" class="mb-4 p-4 rounded-md bg-red-100 dark:bg-red-800 text-sm font-medium text-red-700 dark:text-red-200">
        {{ flash.error }}
      </div>

      <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-4">
        <div class="relative w-full md:max-w-xs">
          <Input
            type="text"
            v-model="search"
            placeholder="Buscar por nome, email, CNPJ..."
            class="pl-10 w-full bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600"
          />
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <Search class="h-5 w-5 text-gray-400" />
          </div>
        </div>
        <Link :href="route('clients.create')" class="w-full md:w-auto">
          <Button variant="black" class="w-full bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
            <Plus class="h-4 w-4 mr-2" />
            Adicionar Cliente
          </Button>
        </Link>
      </div>

      <div class="hidden md:block bg-gray-100 dark:bg-gray-800 overflow-x-auto shadow-sm sm:rounded-lg">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead class="px-4 py-3 w-[60px] text-gray-800 dark:text-gray-200">Logo</TableHead>
              <TableHead class="px-4 py-3 text-gray-800 dark:text-gray-200">Nome</TableHead>
              <TableHead class="px-4 py-3 text-gray-800 dark:text-gray-200">Email</TableHead>
              <TableHead class="px-4 py-3 text-gray-800 dark:text-gray-200">CNPJ</TableHead>
              <TableHead class="px-4 py-3 text-gray-800 dark:text-gray-200">Telefone</TableHead>
              <TableHead class="text-right px-4 py-3 text-gray-800 dark:text-gray-200">Ações</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <template v-if="clients.data.length > 0">
              <TableRow v-for="client in clients.data" :key="client.id" class="[&>td]:py-2 border-b dark:border-gray-700">
                <TableCell class="px-4">
                  <Avatar class="h-10 w-10">
                    <AvatarImage :src="client.logo_full_url" :alt="client.user_name" />
                    <AvatarFallback>{{ client.user_name?.substring(0,2).toUpperCase() || 'C' }}</AvatarFallback>
                  </Avatar>
                </TableCell>
                <TableCell class="font-medium px-4 text-gray-800 dark:text-gray-200">{{ client.user_name }}</TableCell>
                <TableCell class="px-4 text-gray-600 dark:text-gray-400">{{ client.user_email }}</TableCell>
                <TableCell class="px-4 text-gray-600 dark:text-gray-400">{{ client.cnpj }}</TableCell>
                <TableCell class="px-4 text-gray-600 dark:text-gray-400">{{ client.phone || 'N/A' }}</TableCell>
                <TableCell class="text-right px-4">
                  <div class="flex items-center justify-end gap-2">
                    <Link :href="route('clients.edit', client.id)">
                      <Button variant="outline" size="icon" class="h-8 w-8">
                        <FileEdit class="h-4 w-4" />
                        <span class="sr-only">Editar</span>
                      </Button>
                    </Link>
                    <Button
                      variant="outline"
                      size="icon"
                      @click="deleteClient(client.id)"
                      class="h-8 w-8 text-red-600 hover:text-red-700 hover:border-red-400 dark:hover:border-red-600"
                    >
                      <Trash2 class="h-4 w-4" />
                      <span class="sr-only">Excluir</span>
                    </Button>
                  </div>
                </TableCell>
              </TableRow>
            </template>
            <TableRow v-else>
              <TableCell colspan="6" class="text-center py-8 text-gray-500 dark:text-gray-400 px-4">
                {{ search ? `Nenhum cliente encontrado para "${search}".` : 'Nenhum cliente cadastrado.' }}
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>

      <div class="md:hidden space-y-4">
        <template v-if="clients.data.length > 0">
          <Card v-for="client in clients.data" :key="`mobile-${client.id}`" class="bg-gray-100 dark:bg-gray-800 shadow-sm">
            <CardHeader class="flex flex-row items-center gap-4 space-y-0 pb-2">
              <Avatar class="h-12 w-12">
                <AvatarImage :src="client.logo_full_url" :alt="client.user_name" />
                <AvatarFallback>{{ client.user_name?.substring(0,2).toUpperCase() || 'C' }}</AvatarFallback>
              </Avatar>
              <div class="flex-1">
                <CardTitle class="text-lg text-gray-800 dark:text-gray-200">{{ client.user_name }}</CardTitle>
                <CardDescription class="text-gray-600 dark:text-gray-400">{{ client.user_email }}</CardDescription>
              </div>
            </CardHeader>
            <CardContent class="text-sm text-gray-700 dark:text-gray-300 space-y-1 pt-2 pb-4 px-6">
              <p><strong class="font-medium text-gray-800 dark:text-gray-200">CNPJ:</strong> {{ client.cnpj }}</p>
              <p><strong class="font-medium text-gray-800 dark:text-gray-200">Telefone:</strong> {{ client.phone || 'N/A' }}</p>
            </CardContent>
            <CardFooter class="flex justify-end gap-2 bg-gray-200/50 dark:bg-gray-900/50 py-3 px-6">
              <Link :href="route('clients.edit', client.id)">
                <Button variant="outline" size="sm">
                  <FileEdit class="h-4 w-4 mr-2" />
                  Editar
                </Button>
              </Link>
              <Button
                variant="outline"
                size="sm"
                @click="deleteClient(client.id)"
                class="text-red-600 hover:text-red-700 hover:border-red-400 dark:hover:border-red-600"
              >
                <Trash2 class="h-4 w-4 mr-2" />
                Excluir
              </Button>
            </CardFooter>
          </Card>
        </template>
        <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
          {{ search ? `Nenhum cliente encontrado para "${search}".` : 'Nenhum cliente cadastrado.' }}
        </div>
      </div>

      <Pagination
        v-if="clients.data.length > 0 && clients.last_page > 1"
        class="mt-6"
        :links="clients.links"
      />
    </div>
  </AuthenticatedLayout>
</template>
