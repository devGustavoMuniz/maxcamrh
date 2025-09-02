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
import EmptyStateMessage from "@/Components/EmptyStateMessage.vue";
import { computed } from "vue";

const { admins, search } = defineProps({
    admins: {
        type: Object,
        default: () => ({}),
    },
    search: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["delete"]);

const deleteAdmin = (adminId) => {
    emit("delete", adminId);
};

const emptyStateMessage = computed(() => {
    return search
        ? `Nenhum administrador encontrado para "${search}".`
        : 'Nenhum administrador cadastrado.';
});
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
                <template v-if="admins.data.length > 0">
                    <TableRow
                        v-for="admin in admins.data"
                        :key="admin.id"
                        class="[&>td]:py-2 border-b dark:border-gray-700"
                    >
                        <TableCell
                            class="font-medium px-4 text-gray-800 dark:text-gray-200"
                            >{{ admin.name }}</TableCell
                        >
                        <TableCell class="px-4 text-gray-600 dark:text-gray-400">{{
                            admin.email
                        }}</TableCell>
                        <TableCell class="text-right px-4">
                            <div class="flex items-center justify-end gap-2">
                                <Link :href="route('admins.edit', admin.id)">
                                    <Button variant="outline" size="icon" class="h-8 w-8">
                                        <FileEdit class="h-4 w-4" />
                                        <span class="sr-only">Editar</span>
                                    </Button>
                                </Link>
                                <Button
                                    variant="outline"
                                    size="icon"
                                    class="h-8 w-8 text-red-600 hover:text-red-700 hover:border-red-400 dark:hover:border-red-600"
                                    @click="deleteAdmin(admin.id)"
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
                    >
                        <EmptyStateMessage
                            :message="emptyStateMessage"
                        />
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
</template>