<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage, router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";
import debounce from "lodash/debounce";

import Pagination from "@/Components/Pagination.vue";
import AdminFilter from "@/Components/Admin/AdminFilter.vue";
import AdminTable from "@/Components/Admin/AdminTable.vue";
import AdminCard from "@/Components/Admin/AdminCard.vue";
import FlashMessages from "@/Components/FlashMessages.vue";

const props = defineProps({
    admins: {
        type: Object,
        default: () => ({}),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const flash = computed(() => usePage().props.flash);
const search = ref(props.filters?.search || "");

watch(
    search,
    debounce((value) => {
        router.get(
            route("admins.index"),
            { search: value },
            {
                preserveState: true,
                replace: true,
                preserveScroll: true,
            },
        );
    }, 300),
);


const deleteAdmin = (adminId) => {
    if (
        confirm(
            "Tem certeza que deseja excluir este administrador? Esta ação não poderá ser desfeita.",
        )
    ) {
        router.delete(route("admins.destroy", adminId), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Administradores" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Gerenciar Administradores
            </h2>
        </template>

        <div class="mx-auto w-full">
            <FlashMessages />

            <AdminFilter :search="search" @update:search="search = $event" />

            <AdminTable :admins="admins" :search="search" @delete="deleteAdmin" />

            <div class="md:hidden space-y-4">
                <template v-if="admins.data.length > 0">
                    <AdminCard
                        v-for="admin in admins.data"
                        :key="`mobile-${admin.id}`"
                        :admin="admin"
                        @delete="deleteAdmin"
                    />
                </template>
                <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                    {{ search ? `Nenhum administrador encontrado para "${search}".` : 'Nenhum administrador cadastrado.' }}
                </div>
            </div>

            <Pagination
                v-if="admins.data.length > 0 && admins.meta.last_page > 1"
                class="mt-6"
                :links="admins.meta.links"
            />
        </div>
    </AuthenticatedLayout>
</template>
