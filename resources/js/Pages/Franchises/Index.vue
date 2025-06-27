<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, usePage, router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";
import debounce from "lodash/debounce";

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

import { Plus, FileEdit, Trash2, Search } from "lucide-vue-next";

import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    franchises: Object,
    filters: Object,
});

const flash = computed(() => usePage().props.flash);
const search = ref(props.filters?.search || "");

watch(
    search,
    debounce((value) => {
        router.get(
            route("franchises.index"),
            { search: value },
            {
                preserveState: true,
                replace: true,
                preserveScroll: true,
            },
        );
    }, 300),
);

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
                <div class="relative w-full md:max-w-xs">
                    <Input
                        type="text"
                        v-model="search"
                        placeholder="Buscar por nome ou email..."
                        class="pl-10 w-full bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600"
                    />
                    <div
                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                    >
                        <Search class="h-5 w-5 text-gray-400" />
                    </div>
                </div>
                <Link :href="route('franchises.create')" class="w-full md:w-auto">
                    <Button
                        variant="black"
                        class="w-full bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600"
                    >
                        <Plus class="h-4 w-4 mr-2" />
                        Adicionar Franqueado
                    </Button>
                </Link>
            </div>

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
                        <template v-if="franchises.data.length > 0">
                            <TableRow
                                v-for="franchise in franchises.data"
                                :key="franchise.id"
                                class="[&>td]:py-2 border-b dark:border-gray-700"
                            >
                                <TableCell
                                    class="font-medium px-4 text-gray-800 dark:text-gray-200"
                                >{{ franchise.user.name }}</TableCell
                                >
                                <TableCell class="px-4 text-gray-600 dark:text-gray-400">{{
                                        franchise.user.email
                                    }}</TableCell>
                                <TableCell class="text-right px-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link :href="route('franchises.edit', franchise.id)">
                                            <Button variant="outline" size="icon" class="h-8 w-8">
                                                <FileEdit class="h-4 w-4" />
                                                <span class="sr-only">Editar</span>
                                            </Button>
                                        </Link>
                                        <Button
                                            variant="outline"
                                            size="icon"
                                            @click="deleteFranchise(franchise.id)"
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
                                colspan="3"
                                class="text-center py-8 text-gray-500 dark:text-gray-400 px-4"
                            >
                                {{
                                    search
                                        ? `Nenhum franqueado encontrado para "${search}".`
                                        : "Nenhum franqueado cadastrado."
                                }}
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div class="md:hidden space-y-4">
                <template v-if="franchises.data.length > 0">
                    <Card
                        v-for="franchise in franchises.data"
                        :key="`mobile-${franchise.id}`"
                        class="bg-gray-100 dark:bg-gray-800 shadow-sm"
                    >
                        <CardHeader>
                            <CardTitle class="text-lg text-gray-800 dark:text-gray-200">{{
                                    franchise.user.name
                                }}</CardTitle>
                            <CardDescription class="text-gray-600 dark:text-gray-400">{{
                                    franchise.user.email
                                }}</CardDescription>
                        </CardHeader>
                        <CardFooter class="flex justify-end gap-2">
                            <Link :href="route('franchises.edit', franchise.id)">
                                <Button variant="outline" size="sm">
                                    <FileEdit class="h-4 w-4 mr-2" />
                                    Editar
                                </Button>
                            </Link>
                            <Button
                                variant="outline"
                                size="sm"
                                @click="deleteFranchise(franchise.id)"
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
                            ? `Nenhum franqueado encontrado para "${search}".`
                            : "Nenhum franqueado cadastrado."
                    }}
                </div>
            </div>

            <Pagination
                v-if="franchises.data.length > 0 && franchises.meta.last_page > 1"
                class="mt-6"
                :links="franchises.meta.links"
            />
        </div>
    </AuthenticatedLayout>
</template>
