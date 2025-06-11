<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import debounce from 'lodash/debounce';

import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

// Ícones
import { UserPlus, FileEdit, Trash2, Search } from 'lucide-vue-next';

import Pagination from '@/components/Pagination.vue';

const props = defineProps({
  admins: Object,
  filters: Object,
});

const flash = computed(() => usePage().props.flash);

const search = ref(props.filters?.search || '');

watch(search, debounce((value) => {
  router.get(route('admins.index'), { search: value }, {
    preserveState: true,
    replace: true,
    preserveScroll: true,
  });
}, 300));

const deleteAdmin = (adminId) => {
  if (confirm('Tem certeza que deseja excluir este administrador? Esta ação não poderá ser desfeita.')) {
    router.delete(route('admins.destroy', adminId), {
      preserveScroll: true,
    });
  }
};
</script>

<template>
  <Head title="Administradores" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Gerenciar Administradores
        </h2>
      </div>
    </template>

    <div class="mx-auto w-full">
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
              placeholder="Buscar por nome ou email..."
              class="pl-10 w-full bg-gray-100 dark:bg-gray-800"
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <Search class="h-5 w-5 text-gray-400" />
            </div>
          </div>
          <Link :href="route('admins.create')">
            <Button variant="black" class="bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
              <UserPlus class="h-4 w-4 mr-2" />
              Adicionar Administrador
            </Button>
          </Link>
        </div>

        <div class="bg-gray-100 dark:bg-gray-800 overflow-x-auto shadow-sm sm:rounded-lg">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead class="px-4 py-3">Nome</TableHead>
                <TableHead class="px-4 py-3">Email</TableHead>
                <TableHead class="text-right px-4 py-3">Ações</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-if="admins.data.length === 0">
                <TableCell colspan="4" class="text-center py-8 text-gray-500 dark:text-gray-400 px-4">
                  {{ search ? 'Nenhum administrador encontrado para "' + search + '".' : 'Nenhum administrador encontrado.' }}
                </TableCell>
              </TableRow>
              <TableRow v-for="admin in admins.data" :key="admin.id" class="[&>td]:py-2">
                <TableCell class="font-medium px-4">{{ admin.name }}</TableCell>
                <TableCell class="px-4">{{ admin.email }}</TableCell>

                <TableCell class="text-right px-4">
                  <div class="flex items-center justify-end gap-2">
                    <Link :href="route('admins.edit', admin.id)">
                      <Button variant="outline" size="icon" class="h-8 w-8">
                        <FileEdit class="h-4 w-4" />
                        <span class="sr-only">Editar</span>
                      </Button>
                    </Link>
                    <Button
                      variant="outline"
                      size="icon"
                      @click="deleteAdmin(admin.id)"
                      class="h-8 w-8 text-red-600 hover:text-red-700 hover:border-red-400 dark:hover:border-red-600"
                    >
                      <Trash2 class="h-4 w-4" />
                      <span class="sr-only">Excluir</span>
                    </Button>
                  </div>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
        <Pagination
          v-if="admins.data.length > 0 && admins.last_page > 1"
          class="mt-6"
          :links="admins.links"
        />
      </div>
  </AuthenticatedLayout>
</template>
