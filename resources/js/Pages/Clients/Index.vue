<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage, router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";
import debounce from "lodash/debounce";

import {
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/Components/ui/table";

import Pagination from "@/Components/Pagination.vue";
import ClientFilter from "@/Components/Client/ClientFilter.vue";
import ClientTableRow from "@/Components/Client/ClientTableRow.vue";
import ClientCard from "@/Components/Client/ClientCard.vue";

const props = defineProps({
    clients: {
        type: Object,
        default: () => ({}),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    franchises: {
        type: Array,
        default: () => [],
    },
});

const flash = computed(() => usePage().props.flash);
const user = computed(() => usePage().props.auth.user);

const search = ref(props.filters?.search || "");
const selectedFranchise = ref(props.filters.franchise_id || "");

watch(
    [search, selectedFranchise],
    debounce(([searchValue, franchiseIdValue]) => {
        const finalFranchiseId =
            franchiseIdValue === null ? "" : franchiseIdValue;
        router.get(
            route("clients.index"),
            { search: searchValue, franchise_id: finalFranchiseId },
            {
                preserveState: true,
                replace: true,
                preserveScroll: true,
            },
        );
    }, 300),
);

const emptyStateMessage = computed(() => {
    return search.value
        ? `Nenhum cliente encontrado para "${search.value}".`
        : 'Nenhum cliente cadastrado.';
});

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

            <ClientFilter
                :search="search"
                :selected-franchise="selectedFranchise"
                :franchises="franchises"
                :user-role="user.role"
                @update:search="search = $event"
                @update:selected-franchise="selectedFranchise = $event"
            />

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
                            <ClientTableRow
                                v-for="client in clients.data"
                                :key="client.id"
                                :client="client"
                                @delete="deleteClient"
                            />
                        </template>
                        <TableRow v-else>
                            <TableCell colspan="7">
                                <EmptyStateMessage
                                    :message="emptyStateMessage"
                                />
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div class="md:hidden space-y-4">
                <template v-if="clients.data.length > 0">
                    <ClientCard
                        v-for="client in clients.data"
                        :key="`mobile-${client.id}`"
                        :client="client"
                        @delete="deleteClient"
                    />
                </template>
                <div v-else>
                    <EmptyStateMessage
                        :message="emptyStateMessage"
                    />
                </div>
            </div>

            <Pagination
                v-if="clients.data.length > 0 && clients.meta.last_page > 1"
                class="mt-6"
                :links="clients.meta.links"
            />
        </div>
    </AuthenticatedLayout>
</template>
