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
import AdminFilter from "@/Components/Admin/AdminFilter.vue";
import AdminTableRow from "@/Components/Admin/AdminTableRow.vue";
import AdminCard from "@/Components/Admin/AdminCard.vue";
import EmptyStateMessage from "@/Components/EmptyStateMessage.vue";

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

const emptyStateMessage = computed(() => {
    return search.value
        ? `Nenhum administrador encontrado para "${search.value}".`
        : 'Nenhum administrador cadastrado.';
});

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

            <AdminFilter :search="search" @update:search="search = $event" />

            <div
                class="hidden md:block bg-gray-100 dark:bg-gray-800 overflow-x-auto shadow-sm sm:rounded-lg"
            >
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="px-4 py-3 text-gray-800 dark:text-gray-200"
                            >Nome</TableHead
                            >
                            <TableHead class="px-4 py-3 text-gray-800 dark:text-gray-200"
                            >Email</TableHead
                            >
                            <TableHead
                                class="text-right px-4 py-3 text-gray-800 dark:text-gray-200"
                            >Ações</TableHead
                            >
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <template v-if="admins.data.length > 0">
                            <AdminTableRow
                                v-for="admin in admins.data"
                                :key="admin.id"
                                :admin="admin"
                                @delete="deleteAdmin"
                            />
                        </template>
                        <TableRow v-else>
                            <TableCell
                                colspan="3"
                            >
                                <EmptyStateMessage
                                    :message="emptyStateMessage"
                                />
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div class="md:hidden space-y-4">
                <template v-if="admins.data.length > 0">
                    <AdminCard
                        v-for="admin in admins.data"
                        :key="`mobile-${admin.id}`"
                        :admin="admin"
                        @delete="deleteAdmin"
                    />
                </template>
                <div v-else>
                    <EmptyStateMessage
                        :message="emptyStateMessage"
                    />
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
