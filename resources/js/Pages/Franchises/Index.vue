<script setup>
 
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import FranchiseFilters from "@/Components/Franchise/FranchiseFilters.vue";
import FranchiseTable from "@/Components/Franchise/FranchiseTable.vue";
import FranchiseMobileCards from "@/Components/Franchise/FranchiseMobileCards.vue";
import FlashMessages from "@/Components/FlashMessages.vue";

const props = defineProps({
    franchises: {
        type: Object,
        default: () => ({}),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const deleteFranchise = (franchiseId) => {
    if (
        confirm(
            "Tem certeza que deseja excluir este franqueado e seu usuário associado? Esta ação não poderá ser desfeita.",
        )
    ) {
        router.delete(route("franchises.destroy", franchiseId), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Franqueados" />
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Gerenciar Franqueados
            </h2>
        </template>

        <div class="mx-auto w-full">
            <FlashMessages />

            <FranchiseFilters :filters="filters" />

            <FranchiseTable :franchises="franchises" @delete="deleteFranchise" />

            <FranchiseMobileCards :franchises="franchises" @delete="deleteFranchise" />

            <Pagination
                v-if="franchises.data.length > 0 && franchises.meta.last_page > 1"
                class="mt-6"
                :links="franchises.meta.links"
            />
        </div>
    </AuthenticatedLayout>
</template>
