<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, usePage, router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";
import debounce from "lodash/debounce";

// Componentes da UI
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/Components/ui/table";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
  CardDescription,
  CardFooter,
} from "@/Components/ui/card";
import { Avatar, AvatarFallback, AvatarImage } from "@/Components/ui/avatar";

// --- NOVO: Importações para o Combobox ---
import { Check, ChevronsUpDown } from "lucide-vue-next";
import { cn } from "@/lib/utils"; // Função utilitária para classes condicionais, se você a tiver
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from "@/Components/ui/command";
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/Components/ui/popover";

// Ícones
import { Plus, FileEdit, Trash2, Search } from "lucide-vue-next";

// Paginação
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
  clients: Object,
  filters: Object,
  // --- NOVO: Prop para a lista de franqueados ---
  franchisees: Array,
});

const flash = computed(() => usePage().props.flash);
const user = computed(() => usePage().props.auth.user); // Adicionado para verificação de permissão, se necessário

// Lógica de Busca e Filtros
const search = ref(props.filters?.search || "");
// --- NOVO: Estado para o franqueado selecionado ---
const selectedFranchisee = ref(props.filters.franchisee_id || "");

// --- NOVO: Estados e Propriedade Computada para o Combobox de Franqueados ---
const openFranchiseeCombobox = ref(false); // Estado para controlar a abertura/fechamento do Popover
const currentFranchiseeLabel = computed(() => {
  // Encontra o nome do franqueado selecionado para exibir no trigger do combobox
  const franchisee = props.franchisees.find(
    (f) => String(f.id) === selectedFranchisee.value,
  );
  return franchisee ? franchisee.name : "Filtrar por franqueado...";
});

watch(
  [search, selectedFranchisee],
  debounce(([searchValue, franchiseeIdValue]) => {
    const finalFranchiseeId =
      franchiseeIdValue === null ? "" : franchiseeIdValue;
    router.get(
      route("clients.index"),
      { search: searchValue, franchise_id: finalFranchiseeId },
      {
        preserveState: true,
        replace: true,
        preserveScroll: true,
      },
    );
  }, 300),
);

// Função para deletar
const deleteClient = (clientId) => {
  if (
    confirm(
      "Tem certeza que deseja excluir este cliente e seu usuário associado? Todos os colaboradores vinculados também podem ser afetados ou excluídos. Esta ação não poderá ser desfeita.",
    )
  ) {
    router.delete(route("clients.destroy", clientId), {
      preserveScroll: true,
    });
  }
};
</script>

<template>
  <Head title="Clientes" />
  <AuthenticatedLayout>
    <template #header>
      <h2
        class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
      >
        Gerenciar Clientes
      </h2>
    </template>

    <div class="mx-auto w-full">
      <div
        v-if="flash && flash.success"
        class="mb-4 p-4 rounded-md bg-green-100 dark:bg-green-800 text-sm font-medium text-green-700 dark:text-green-200"
      >
        {{ flash.success }}
      </div>
      <div
        v-if="flash && flash.error"
        class="mb-4 p-4 rounded-md bg-red-100 dark:bg-red-800 text-sm font-medium text-red-700 dark:text-red-200"
      >
        {{ flash.error }}
      </div>

      <div
        class="flex flex-col md:flex-row justify-between items-center gap-4 mb-4"
      >
        <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
          <div class="relative w-full sm:max-w-xs">
            <Input
              type="text"
              v-model="search"
              placeholder="Buscar por nome, email, CNPJ..."
              class="pl-10 w-full bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600"
            />
            <div
              class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
            >
              <Search class="h-5 w-5 text-gray-400" />
            </div>
          </div>

          <div v-if="user.role === 'admin'" class="w-full sm:w-auto">
            <Popover v-model:open="openFranchiseeCombobox">
              <PopoverTrigger as-child>
                <Button
                  variant="outline"
                  role="combobox"
                  :aria-expanded="openFranchiseeCombobox"
                  class="w-full sm:w-[280px] justify-between bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600"
                >
                  {{
                    selectedFranchisee
                      ? currentFranchiseeLabel
                      : "Filtrar por franqueado..."
                  }}
                  <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                </Button>
              </PopoverTrigger>
              <PopoverContent class="w-full sm:w-[280px] p-0">
                <Command>
                  <CommandInput
                    class="h-4 my-2"
                    placeholder="Buscar franqueado..."
                  />
                  <CommandList>
                    <CommandEmpty>Nenhum franqueado encontrado.</CommandEmpty>
                    <CommandGroup>
                      <CommandItem
                        :value="null"
                        @select="
                          () => {
                            selectedFranchisee = null;
                            openFranchiseeCombobox = false;
                          }
                        "
                      >
                        <Check
                          :class="
                            cn(
                              'mr-2 h-4 w-4',
                              selectedFranchisee === null
                                ? 'opacity-100'
                                : 'opacity-0',
                            )
                          "
                        />
                        Todos os Franqueados
                      </CommandItem>
                      <CommandItem
                        v-for="franchisee in props.franchisees"
                        :key="franchisee.id"
                        :value="franchisee.name"
                        @select="
                          () => {
                            selectedFranchisee = String(franchisee.id);
                            openFranchiseeCombobox = false;
                          }
                        "
                      >
                        <Check
                          :class="
                            cn(
                              'mr-2 h-4 w-4',
                              selectedFranchisee === String(franchisee.id)
                                ? 'opacity-100'
                                : 'opacity-0',
                            )
                          "
                        />
                        {{ franchisee.name }}
                      </CommandItem>
                    </CommandGroup>
                  </CommandList>
                </Command>
              </PopoverContent>
            </Popover>
          </div>
        </div>
        <Link :href="route('clients.create')" class="w-full md:w-auto">
          <Button
            variant="black"
            class="w-full bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600"
          >
            <Plus class="h-4 w-4 mr-2" />
            Adicionar Cliente
          </Button>
        </Link>
      </div>

      <div
        class="hidden md:block bg-gray-100 dark:bg-gray-800 overflow-x-auto shadow-sm sm:rounded-lg"
      >
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead
                class="px-4 py-3 w-[60px] text-gray-800 dark:text-gray-200"
                >Logo</TableHead
              >
              <TableHead class="px-4 py-3 text-gray-800 dark:text-gray-200"
                >Nome</TableHead
              >
              <TableHead class="px-4 py-3 text-gray-800 dark:text-gray-200"
                >Email</TableHead
              >
              <TableHead class="px-4 py-3 text-gray-800 dark:text-gray-200"
                >CNPJ</TableHead
              >
              <TableHead class="px-4 py-3 text-gray-800 dark:text-gray-200"
                >Telefone</TableHead
              >
              <TableHead class="px-4 py-3 text-gray-800 dark:text-gray-200"
                >Franqueado Associado</TableHead
              >
              <TableHead
                class="text-right px-4 py-3 text-gray-800 dark:text-gray-200"
                >Ações</TableHead
              >
            </TableRow>
          </TableHeader>
          <TableBody>
            <template v-if="clients.data.length > 0">
              <TableRow
                v-for="client in clients.data"
                :key="client.id"
                class="[&>td]:py-2 border-b dark:border-gray-700"
              >
                <TableCell class="px-4">
                  <Avatar class="h-10 w-10">
                    <AvatarImage
                      :src="client.logo_full_url"
                      :alt="client.user_name"
                    />
                    <AvatarFallback>{{
                      client.user_name?.substring(0, 2).toUpperCase() || "C"
                    }}</AvatarFallback>
                  </Avatar>
                </TableCell>
                <TableCell
                  class="font-medium px-4 text-gray-800 dark:text-gray-200"
                  >{{ client.user_name }}</TableCell
                >
                <TableCell class="px-4 text-gray-600 dark:text-gray-400">{{
                  client.user_email
                }}</TableCell>
                <TableCell class="px-4 text-gray-600 dark:text-gray-400">{{
                  client.cnpj
                }}</TableCell>
                <TableCell class="px-4 text-gray-600 dark:text-gray-400">{{
                  client.phone || "N/A"
                }}</TableCell>
                <TableCell class="px-4 text-gray-600 dark:text-gray-400">{{
                  client.franchise_name || "N/A"
                }}</TableCell>
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
              <TableCell
                colspan="7"
                class="text-center py-8 text-gray-500 dark:text-gray-400 px-4"
              >
                {{
                  search
                    ? `Nenhum cliente encontrado para "${search}".`
                    : "Nenhum cliente cadastrado."
                }}
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>

      <div class="md:hidden space-y-4">
        <template v-if="clients.data.length > 0">
          <Card
            v-for="client in clients.data"
            :key="`mobile-${client.id}`"
            class="bg-gray-100 dark:bg-gray-800 shadow-sm"
          >
            <CardHeader class="flex flex-row items-center gap-4 space-y-0 pb-2">
              <Avatar class="h-12 w-12">
                <AvatarImage
                  :src="client.logo_full_url"
                  :alt="client.user_name"
                />
                <AvatarFallback>{{
                  client.user_name?.substring(0, 2).toUpperCase() || "C"
                }}</AvatarFallback>
              </Avatar>
              <div class="flex-1">
                <CardTitle class="text-lg text-gray-800 dark:text-gray-200">{{
                  client.user_name
                }}</CardTitle>
                <CardDescription class="text-gray-600 dark:text-gray-400">{{
                  client.user_email
                }}</CardDescription>
              </div>
            </CardHeader>
            <CardContent
              class="text-sm text-gray-700 dark:text-gray-300 space-y-1 pt-2 pb-4 px-6"
            >
              <p>
                <strong class="font-medium text-gray-800 dark:text-gray-200"
                  >CNPJ:</strong
                >
                {{ client.cnpj }}
              </p>
              <p>
                <strong class="font-medium text-gray-800 dark:text-gray-200"
                  >Telefone:</strong
                >
                {{ client.phone || "N/A" }}
              </p>
              <p v-if="client.franchisee_name">
                <strong class="font-medium text-gray-800 dark:text-gray-200"
                  >Franqueado:</strong
                >
                {{ client.franchisee_name }}
              </p>
            </CardContent>
            <CardFooter
              class="flex justify-end gap-2 bg-gray-200/50 dark:bg-gray-900/50 py-3 px-6"
            >
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
          {{
            search
              ? `Nenhum cliente encontrado para "${search}".`
              : "Nenhum cliente cadastrado."
          }}
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
