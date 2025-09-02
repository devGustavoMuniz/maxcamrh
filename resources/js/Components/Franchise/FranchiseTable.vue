<script setup>
import { Link } from "@inertiajs/vue3";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/Components/ui/table";
import { Button } from "@/Components/ui/button";
import { FileEdit, Trash2 } from "lucide-vue-next";

const { franchises } = defineProps({
    franchises: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(["delete"]);

const deleteFranchise = (franchiseId) => {
    emit("delete", franchiseId);
};
</script>

<template>
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
                        >E-mail</TableHead
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
                                    class="h-8 w-8 text-red-600 hover:text-red-700 hover:border-red-400 dark:hover:border-red-600"
                                    @click="deleteFranchise(franchise.id)"
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
                        Nenhum franqueado cadastrado.
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
</template>
