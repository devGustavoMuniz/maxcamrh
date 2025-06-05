<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3'; // Importado o router
import { computed } from 'vue';

// Shadcn Vue Table Components
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

// Ícones Lucide Vue Next
import { MoreHorizontal, UserPlus, Eye, FileEdit, Trash2 } from 'lucide-vue-next';

// Para paginação
import Pagination from '@/components/Pagination.vue';

defineProps({
  admins: Object, // Objeto de paginação do Laravel
});

const flash = computed(() => usePage().props.flash);

// Função para exclusão
const deleteAdmin = (adminId) => {
  if (confirm('Tem certeza que deseja excluir este administrador?')) {
    router.delete(route('admins.destroy', adminId), {
      preserveScroll: true,
      // onSuccess e onError podem ser usados para feedback mais robusto
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
        <Link :href="route('admins.create')">
          <Button variant="default">
            <UserPlus class="h-4 w-4 mr-2" />
            Adicionar Administrador
          </Button>
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div v-if="flash && flash.success" class="mb-4 p-4 bg-green-100 text-green-700 dark:bg-green-700 dark:text-green-100 rounded">
          {{ flash.success }}
        </div>
        <div v-if="flash && flash.error" class="mb-4 p-4 bg-red-100 text-red-700 dark:bg-red-700 dark:text-red-100 rounded">
          {{ flash.error }}
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Nome</TableHead>
                <TableHead>Email</TableHead>
                <TableHead>Criado em</TableHead>
                <TableHead class="text-right">Ações</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-if="admins.data.length === 0">
                <TableCell colspan="4" class="text-center">Nenhum administrador encontrado.</TableCell>
              </TableRow>
              <TableRow v-for="admin in admins.data" :key="admin.id">
                <TableCell>{{ admin.name }}</TableCell>
                <TableCell>{{ admin.email }}</TableCell>
                <TableCell>{{ admin.created_at }}</TableCell>
                <TableCell class="text-right">
                  <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                      <Button variant="ghost" class="h-8 w-8 p-0">
                        <span class="sr-only">Abrir menu</span>
                        <MoreHorizontal class="h-4 w-4" />
                      </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                      <DropdownMenuItem as-child>
                        <Link :href="route('admins.show', admin.id)" class="flex items-center w-full">
                          <Eye class="h-4 w-4 mr-2" />
                          Visualizar
                        </Link>
                      </DropdownMenuItem>
                      <DropdownMenuItem as-child>
                        <Link :href="route('admins.edit', admin.id)" class="flex items-center w-full">
                          <FileEdit class="h-4 w-4 mr-2" />
                          Editar
                        </Link>
                      </DropdownMenuItem>
                      <DropdownMenuItem @click="deleteAdmin(admin.id)" class="text-red-600 dark:hover:text-red-400 flex items-center cursor-pointer">
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
        <Pagination class="mt-6" :links="admins.links" />
      </div>
    </div>
  </AuthenticatedLayout>
</template>

