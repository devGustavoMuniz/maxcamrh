<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import debounce from 'lodash/debounce';

import {
  Table, TableBody, TableCell, TableHead, TableHeader, TableRow,
} from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
  DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

import { MoreHorizontal, Plus, Eye, FileEdit, Trash2, Search } from 'lucide-vue-next';

import Pagination from '@/components/Pagination.vue';

const props = defineProps({
  franchises: Object,
  filters: Object,
});

const flash = computed(() => usePage().props.flash);

const search = ref(props.filters?.search || '');

watch(search, debounce((value) => {
  router.get(route('franchises.index'), { search: value }, {
    preserveState: true,
    replace: true,
    preserveScroll: true,
  });
}, 300));

const deleteFranchise = (franchiseId) => {
  if (confirm('Tem certeza que deseja excluir este franqueado e seu usuário associado? Esta ação não poderá ser desfeita.')) {
    router.delete(route('franchises.destroy', franchiseId), {
      preserveScroll: true,
    });
  }
};
</script>

<template>
  <Head title="Franqueados" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Gerenciar Franqueados
        </h2>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div v-if="flash && flash.success" class="mb-4 p-4 rounded-md bg-green-100 dark:bg-green-700 text-sm font-medium text-green-700 dark:text-green-100">
          {{ flash.success }}
        </div>
        <div v-if="flash && flash.error" class="mb-4 p-4 rounded-md bg-red-100 dark:bg-red-700 text-sm font-medium text-red-700 dark:text-red-100">
          {{ flash.error }}
        </div>

        <div class="flex justify-between items-center mb-4">
          <div class="relative w-full max-w-xs">
            <Input
              type="text"
              v-model="search"
              placeholder="Buscar por nome, CNPJ, email..."
              class="pl-10 w-full bg-white dark:bg-gray-800"
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <Search class="h-5 w-5 text-gray-400" />
            </div>
          </div>
          <Link :href="route('franchises.create')">
            <Button variant="default" class="bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
              <Plus class="h-4 w-4 mr-2" />
              Adicionar Franqueado
            </Button>
          </Link>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-x-auto shadow-sm sm:rounded-lg">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead class="px-6 py-4">Nome (Usuário)</TableHead>
                <TableHead class="px-6 py-4">Email (Usuário)</TableHead>
                <TableHead class="px-6 py-4">CNPJ</TableHead>
                <TableHead class="px-6 py-4">Email MaxCam</TableHead>
                <TableHead class="px-6 py-4">Região de Atuação</TableHead>
                <TableHead class="text-right w-[80px] px-6 py-4">Ações</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-if="franchises.data.length === 0">
                <TableCell colspan="6" class="text-center py-10 text-gray-500 dark:text-gray-400 px-6">
                  {{ search ? 'Nenhum franqueado encontrado para "' + search + '".' : 'Nenhum franqueado encontrado.' }}
                </TableCell>
              </TableRow>
              <TableRow v-for="franchise in franchises.data" :key="franchise.id">
                <TableCell class="font-medium px-6">{{ franchise.user_name }}</TableCell>
                <TableCell class="px-6">{{ franchise.user_email }}</TableCell>
                <TableCell class="px-6">{{ franchise.cnpj }}</TableCell>
                <TableCell class="px-6">{{ franchise.maxcam_email }}</TableCell>
                <TableCell class="px-6">{{ franchise.actuation_region }}</TableCell>
                <TableCell class="text-right px-6">
                  <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                      <Button variant="ghost" class="h-8 w-8 p-0">
                        <span class="sr-only">Abrir menu</span>
                        <MoreHorizontal class="h-4 w-4" />
                      </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                      <DropdownMenuItem as-child>
                        <Link :href="route('franchises.show', franchise.id)" class="flex items-center w-full">
                          <Eye class="h-4 w-4 mr-2" />
                          Visualizar
                        </Link>
                      </DropdownMenuItem>
                      <DropdownMenuItem as-child>
                        <Link :href="route('franchises.edit', franchise.id)" class="flex items-center w-full">
                          <FileEdit class="h-4 w-4 mr-2" />
                          Editar
                        </Link>
                      </DropdownMenuItem>
                      <DropdownMenuItem @click="deleteFranchise(franchise.id)" class="text-red-600 dark:hover:text-red-400 hover:text-red-700 flex items-center cursor-pointer">
                        <Trash2 class="h-4 w-4 mr-2" />
                        Excluir
                      </DropdownMenuItem>
                    </DropdownMenuContent>
                  </DropdownMenu>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
        <Pagination
          v-if="franchises.data.length > 0 && franchises.last_page > 1"
          class="mt-6"
          :links="franchises.links"
        />
      </div>
    </div>
  </AuthenticatedLayout>
</template>
