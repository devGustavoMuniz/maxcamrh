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
import { Avatar, AvatarFallback, AvatarImage } from "@/Components/ui/avatar";
import { FileEdit, Trash2 } from "lucide-vue-next";

const { collaborators } = defineProps({
    collaborators: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(["delete"]);

const deleteCollaborator = (collaboratorId) => {
    emit("delete", collaboratorId);
};
</script>

<template>
    <div
        class="hidden md:block bg-gray-100 dark:bg-gray-800 overflow-x-auto shadow-sm sm:rounded-lg"
    >
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead
                        class="px-4 py-3 w-[60px] text-gray-800 dark:text-gray-200"
                        >Foto</TableHead
                    >
                    <TableHead class="px-4 py-3 text-gray-800 dark:text-gray-200"
                        >Nome</TableHead
                    >
                    <TableHead class="px-4 py-3 text-gray-800 dark:text-gray-200"
                        >Email</TableHead
                    >
                    <TableHead class="px-4 py-3 text-gray-800 dark:text-gray-200"
                        >Cliente Associado</TableHead
                    >
                    <TableHead class="px-4 py-3 text-gray-800 dark:text-gray-200"
                        >Cidade</TableHead
                    >
                    <TableHead
                        class="text-right px-4 py-3 text-gray-800 dark:text-gray-200"
                        >Ações</TableHead
                    >
                </TableRow>
            </TableHeader>
            <TableBody>
                <template v-if="collaborators.data.length > 0">
                    <TableRow
                        v-for="collab in collaborators.data"
                        :key="collab.id"
                        class="[&>td]:py-2 border-b dark:border-gray-700"
                    >
                        <TableCell class="px-4">
                            <Avatar class="h-10 w-10">
                                <AvatarImage
                                    :src="collab.photo_full_url"
                                    :alt="collab.user.name"
                                />
                                <AvatarFallback>{{
                                    collab.user.name?.substring(0, 2).toUpperCase() || "C"
                                }}</AvatarFallback>
                            </Avatar>
                        </TableCell>
                        <TableCell
                            class="font-medium px-4 text-gray-800 dark:text-gray-200"
                            >{{ collab.user.name }}</TableCell
                        >
                        <TableCell class="px-4 text-gray-600 dark:text-gray-400">{{
                            collab.user.email
                        }}</TableCell>
                        <TableCell class="px-4 text-gray-600 dark:text-gray-400">{{
                            collab.client_name ?? "N/A"
                        }}</TableCell>
                        <TableCell class="px-4 text-gray-600 dark:text-gray-400">{{
                            collab.address?.city || "N/A"
                        }}</TableCell>
                        <TableCell class="text-right px-4">
                            <div class="flex items-center justify-end gap-2">
                                <Link :href="route('collaborators.edit', collab.id)">
                                    <Button variant="outline" size="icon" class="h-8 w-8">
                                        <FileEdit class="h-4 w-4" />
                                        <span class="sr-only">Editar</span>
                                    </Button>
                                </Link>
                                <Button
                                    variant="outline"
                                    size="icon"
                                    class="h-8 w-8 text-red-600 hover:text-red-700 hover:border-red-400 dark:hover:border-red-600"
                                    @click="deleteCollaborator(collab.id)"
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
                        colspan="6"
                        class="text-center py-8 text-gray-500 dark:text-gray-400 px-4"
                    >
                        {{
                            "Nenhum colaborador cadastrado."
                        }}
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
</template>
