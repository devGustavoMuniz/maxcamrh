<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import FlashMessages from "@/Components/FlashMessages.vue";
import CollaboratorFilters from "@/Components/Collaborator/CollaboratorFilters.vue";
import CollaboratorTable from "@/Components/Collaborator/CollaboratorTable.vue";
import CollaboratorMobileCards from "@/Components/Collaborator/CollaboratorMobileCards.vue";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    collaborators: {
        type: Object,
        default: () => ({}),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    clients: {
        type: Array,
        default: () => [],
    },
});

const search = ref(props.filters.search);
const selectedClient = ref(props.filters.client_id);

watch([search, selectedClient], ([newSearch, newSelectedClient]) => {
    router.get(
        route("collaborators.index"),
        {
            search: newSearch,
            client_id: newSelectedClient,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
});

const deleteCollaborator = (collaboratorId) => {
    if (
        confirm(
            "Tem certeza que deseja excluir este colaborador, seu usuário e todos os dados associados? Esta ação não poderá ser desfeita.",
        )
    ) {
        router.delete(route("collaborators.destroy", collaboratorId), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Colaboradores" />
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Gerenciar Colaboradores
            </h2>
        </template>

        <div class="mx-auto w-full">
            <FlashMessages />

            <CollaboratorFilters
                v-model:search="search"
                v-model:client-id="selectedClient"
                :filters="filters"
                :clients="clients"
            />

            <CollaboratorTable :collaborators="collaborators" @delete="deleteCollaborator" />

            <CollaboratorMobileCards :collaborators="collaborators" @delete="deleteCollaborator" />

            <Pagination
                v-if="collaborators.data.length > 0 && collaborators.meta.last_page > 1"
                class="mt-6"
                :links="collaborators.meta.links"
            />
        </div>
    </AuthenticatedLayout>
</template>
