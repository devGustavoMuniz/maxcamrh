<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
  Table, TableBody, TableCell, TableHead, TableHeader, TableRow,
} from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import {
  DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import Pagination from '@/components/Pagination.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'


defineProps({
  collaborators: Object,
});

const flash = computed(() => usePage().props.flash);

const deleteCollaborator = (collaboratorId) => {
  if (confirm('Tem certeza que deseja excluir este colaborador, seu usuário e todos os dados associados?')) {
    router.delete(route('collaborators.destroy', collaboratorId), {
      preserveScroll: true,
    });
  }
};
</script>

<template>
  <Head title="Colaboradores" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Gerenciar Colaboradores
        </h2>
        <Link :href="route('collaborators.create')">
          <Button variant="default">Adicionar Colaborador</Button>
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
                <TableHead>Foto</TableHead>
                <TableHead>Nome (Usuário)</TableHead>
                <TableHead>Email (Usuário)</TableHead>
                <TableHead>Departamento</TableHead>
                <TableHead>Cargo</TableHead>
                <TableHead>Cidade</TableHead>
                <TableHead class="text-right">Ações</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-if="collaborators.data.length === 0">
                <TableCell colspan="7" class="text-center">Nenhum colaborador encontrado.</TableCell>
              </TableRow>
              <TableRow v-for="collab in collaborators.data" :key="collab.id">
                <TableCell>
                  <Avatar>
                    <AvatarImage :src="collab.photo_full_url" :alt="collab.user_name" />
                    <AvatarFallback>{{ collab.user_name?.substring(0,2).toUpperCase() || 'C' }}</AvatarFallback>
                  </Avatar>
                </TableCell>
                <TableCell>{{ collab.user_name }}</TableCell>
                <TableCell>{{ collab.user_email }}</TableCell>
                <TableCell>{{ collab.department || 'N/A' }}</TableCell>
                <TableCell>{{ collab.position || 'N/A' }}</TableCell>
                <TableCell>{{ collab.city || 'N/A' }}</TableCell>
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
                        <Link :href="route('collaborators.show', collab.id)">Visualizar</Link>
                      </DropdownMenuItem>
                      <DropdownMenuItem as-child>
                        <Link :href="route('collaborators.edit', collab.id)">Editar</Link>
                      </DropdownMenuItem>
                      <DropdownMenuItem @click="deleteCollaborator(collab.id)" class="text-red-600">
                        Excluir
                      </DropdownMenuItem>
                    </DropdownMenuContent>
                  </DropdownMenu>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
        <Pagination class="mt-6" :links="collaborators.links" />
      </div>
    </div>
  </AuthenticatedLayout>
</template>
