<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, usePage, router} from '@inertiajs/vue3';
import {computed, ref, watch} from 'vue';
import debounce from 'lodash/debounce';

// Componentes da UI
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import {
  Table, TableBody, TableCell, TableHead, TableHeader, TableRow,
} from '@/components/ui/table';
import {Button} from '@/components/ui/button';
import {Input} from '@/components/ui/input';
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
  CardDescription,
  CardFooter
} from '@/components/ui/card';
import {Avatar, AvatarFallback, AvatarImage} from '@/components/ui/avatar';
import Pagination from '@/components/Pagination.vue';

// Ícones
import {UserPlus, FileEdit, Trash2, Search} from 'lucide-vue-next';

const props = defineProps({
  collaborators: Object,
  filters: Object,
  clients: Array,
});

// Lógica de Filtros e Busca
const user = computed(() => usePage().props.auth.user);
const flash = computed(() => usePage().props.flash);
const search = ref(props.filters?.search || '');
const selectedClient = ref(props.filters.client_id || '');

watch([search, selectedClient], debounce(([searchValue, clientIdValue]) => {
  // Converte null para uma string vazia para a URL
  const finalClientId = clientIdValue === null ? '' : clientIdValue;
  router.get(route('collaborators.index'), {search: searchValue, client_id: finalClientId}, {
    preserveState: true,
    replace: true,
    preserveScroll: true,
  });
}, 300));

// Função para Deletar
const deleteCollaborator = (collaboratorId) => {
  if (confirm('Tem certeza que deseja excluir este colaborador, seu usuário e todos os dados associados? Esta ação não poderá ser desfeita.')) {
    router.delete(route('collaborators.destroy', collaboratorId), {
      preserveScroll: true,
    });
  }
};
</script>

<template>
  <Head title="Colaboradores"/>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Gerenciar Colaboradores
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
        <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
          <div class="relative w-full sm:max-w-xs">
            <Input
              type="text"
              v-model="search"
              placeholder="Buscar por nome, email..."
              class="pl-10 w-full bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600"
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <Search class="h-5 w-5 text-gray-400"/>
            </div>
          </div>
          <div v-if="user.role === 'admin' || user.role === 'franchise'" class="w-full sm:w-auto">
            <Select v-model="selectedClient">
              <SelectTrigger class="w-full sm:w-[280px] bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600">
                <SelectValue placeholder="Filtrar por cliente" />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  <SelectItem :value="null">
                    Todos os Clientes
                  </SelectItem>
                  <SelectItem v-for="client in props.clients" :key="client.id" :value="String(client.id)">
                    {{ client.name }}
                  </SelectItem>
                </SelectGroup>
              </SelectContent>
            </Select>
          </div>
        </div>

        <Link :href="route('collaborators.create')" class="w-full md:w-auto">
          <Button variant="black" class="w-full bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
            <UserPlus class="h-4 w-4 mr-2"/>
            Adicionar Colaborador
          </Button>
        </Link>
      </div>

      <div class="hidden md:block bg-gray-100 dark:bg-gray-800 overflow-x-auto shadow-sm sm:rounded-lg">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead class="px-4 py-3 w-[60px] text-gray-800 dark:text-gray-200">Foto</TableHead>
              <TableHead class="px-4 py-3 text-gray-800 dark:text-gray-200">Nome</TableHead>
              <TableHead class="px-4 py-3 text-gray-800 dark:text-gray-200">Email</TableHead>
              <TableHead class="px-4 py-3 text-gray-800 dark:text-gray-200">Cliente Associado</TableHead>
              <TableHead class="px-4 py-3 text-gray-800 dark:text-gray-200">Cidade</TableHead>
              <TableHead class="text-right px-4 py-3 text-gray-800 dark:text-gray-200">Ações</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <template v-if="collaborators.data.length > 0">
              <TableRow v-for="collab in collaborators.data" :key="collab.id" class="[&>td]:py-2 border-b dark:border-gray-700">
                <TableCell class="px-4">
                  <Avatar class="h-10 w-10">
                    <AvatarImage :src="collab.photo_full_url" :alt="collab.user_name"/>
                    <AvatarFallback>{{ collab.user_name?.substring(0, 2).toUpperCase() || 'C' }}</AvatarFallback>
                  </Avatar>
                </TableCell>
                <TableCell class="font-medium px-4 text-gray-800 dark:text-gray-200">{{ collab.user_name }}</TableCell>
                <TableCell class="px-4 text-gray-600 dark:text-gray-400">{{ collab.user_email }}</TableCell>
                <TableCell class="px-4 text-gray-600 dark:text-gray-400">{{ collab.client_name}}</TableCell>
                <TableCell class="px-4 text-gray-600 dark:text-gray-400">{{ collab.city || 'N/A' }}</TableCell>
                <TableCell class="text-right px-4">
                  <div class="flex items-center justify-end gap-2">
                    <Link :href="route('collaborators.edit', collab.id)">
                      <Button variant="outline" size="icon" class="h-8 w-8">
                        <FileEdit class="h-4 w-4"/>
                        <span class="sr-only">Editar</span>
                      </Button>
                    </Link>
                    <Button
                      variant="outline"
                      size="icon"
                      @click="deleteCollaborator(collab.id)"
                      class="h-8 w-8 text-red-600 hover:text-red-700 hover:border-red-400 dark:hover:border-red-600"
                    >
                      <Trash2 class="h-4 w-4"/>
                      <span class="sr-only">Excluir</span>
                    </Button>
                  </div>
                </TableCell>
              </TableRow>
            </template>
            <TableRow v-else>
              <TableCell colspan="6" class="text-center py-8 text-gray-500 dark:text-gray-400 px-4">
                {{ search ? `Nenhum colaborador encontrado para "${search}".` : 'Nenhum colaborador cadastrado.' }}
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>

      <div class="md:hidden space-y-4">
        <template v-if="collaborators.data.length > 0">
          <Card v-for="collab in collaborators.data" :key="`mobile-${collab.id}`" class="bg-gray-100 dark:bg-gray-800 shadow-sm">
            <CardHeader class="flex flex-row items-center gap-4 space-y-0 pb-2">
              <Avatar class="h-12 w-12">
                <AvatarImage :src="collab.photo_full_url" :alt="collab.user_name"/>
                <AvatarFallback>{{ collab.user_name?.substring(0, 2).toUpperCase() || 'C' }}</AvatarFallback>
              </Avatar>
              <div class="flex-1">
                <CardTitle class="text-lg text-gray-800 dark:text-gray-200">{{ collab.user_name }}</CardTitle>
                <CardDescription class="text-gray-600 dark:text-gray-400">{{ collab.user_email }}</CardDescription>
              </div>
            </CardHeader>
            <CardContent class="text-sm text-gray-700 dark:text-gray-300 space-y-1 pt-2 pb-4 px-6">
              <p><strong class="font-medium text-gray-800 dark:text-gray-200">Cliente:</strong> {{ collab.client_name }}</p>
              <p><strong class="font-medium text-gray-800 dark:text-gray-200">Cidade:</strong> {{ collab.city || 'N/A' }}</p>
            </CardContent>
            <CardFooter class="flex justify-end gap-2 bg-gray-200/50 dark:bg-gray-900/50 py-3 px-6">
              <Link :href="route('collaborators.edit', collab.id)">
                <Button variant="outline" size="sm">
                  <FileEdit class="h-4 w-4 mr-2" />
                  Editar
                </Button>
              </Link>
              <Button
                variant="outline"
                size="sm"
                @click="deleteCollaborator(collab.id)"
                class="text-red-600 hover:text-red-700 hover:border-red-400 dark:hover:border-red-600"
              >
                <Trash2 class="h-4 w-4 mr-2" />
                Excluir
              </Button>
            </CardFooter>
          </Card>
        </template>
        <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
          {{ search ? `Nenhum colaborador encontrado para "${search}".` : 'Nenhum colaborador cadastrado.' }}
        </div>
      </div>


      <Pagination
        v-if="collaborators.data.length > 0 && collaborators.last_page > 1"
        class="mt-6"
        :links="collaborators.links"
      />
    </div>
  </AuthenticatedLayout>
</template>
