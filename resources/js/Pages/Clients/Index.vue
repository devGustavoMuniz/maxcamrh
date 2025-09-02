<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage, router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";
import debounce from "lodash/debounce";

import Pagination from "@/Components/Pagination.vue";
import ClientFilter from "@/Components/Client/ClientFilter.vue";
import ClientTable from "@/Components/Client/ClientTable.vue";
import ClientCard from "@/Components/Client/ClientCard.vue";
import FlashMessages from "@/Components/FlashMessages.vue";
import EmptyStateMessage from "@/Components/EmptyStateMessage.vue";

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
            <FlashMessages />

            <ClientFilter
                :search="search"
                :selected-franchise="selectedFranchise"
                :franchises="franchises"
                :user-role="user.role"
                @update:search="search = $event"
                @update:selected-franchise="selectedFranchise = $event"
            />

            <ClientTable :clients="clients" :search="search" @delete="deleteClient" />

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
                        :message="search ? `Nenhum cliente encontrado para &quot;${search}&quot;.` : 'Nenhum cliente cadastrado.'"
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
