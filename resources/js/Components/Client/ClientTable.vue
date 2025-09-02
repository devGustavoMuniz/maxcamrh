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
import { Avatar, AvatarFallback, AvatarImage } from "@/Components/ui/avatar";
import { Button } from "@/Components/ui/button";
import { FileEdit, Trash2 } from "lucide-vue-next";
import EmptyStateMessage from "@/Components/EmptyStateMessage.vue";
import { computed } from "vue";

const { clients, search } = defineProps({
    clients: {
        type: Object,
        default: () => ({}),
    },
    search: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["delete"]);

const deleteClient = (clientId) => {
    emit("delete", clientId);
};

const emptyStateMessage = computed(() => {
    return search
        ? `Nenhum cliente encontrado para "${search}".`
        : 'Nenhum cliente cadastrado.';
});
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
                        >Logo</TableHead
                    >
                    <TableHead class="px-4 py-3 text-gray-800 dark:text-gray-200"
                        >Nome</TableHead
                    >
                    <TableHead class="px-4 py-3 text-gray-800 dark:text-gray-200"
                        >E-mail</TableHead
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
                    <TableRow
                        v-for="client in clients.data"
                        :key="client.id"
                        class="[&>td]:py-2 border-b dark:border-gray-700"
                    >
                        <TableCell class="px-4">
                            <Avatar class="h-10 w-10">
                                <AvatarImage :src="client.logo_full_url" :alt="client.user.name" />
                                <AvatarFallback>{{ client.user.name?.substring(0, 2).toUpperCase() || "C" }}</AvatarFallback>
                            </Avatar>
                        </TableCell>
                        <TableCell class="font-medium px-4 text-gray-800 dark:text-gray-200">{{ client.user.name }}</TableCell>
                        <TableCell class="px-4 text-gray-600 dark:text-gray-400">{{ client.user.email }}</TableCell>
                        <TableCell class="px-4 text-gray-600 dark:text-gray-400">{{ client.cnpj }}</TableCell>
                        <TableCell class="px-4 text-gray-600 dark:text-gray-400">{{ client.phone || "Indisponível" }}</TableCell>
                        <TableCell class="px-4 text-gray-600 dark:text-gray-400">{{ client.franchise_name || "Indisponível" }}</TableCell>
                        <TableCell class="text-right px-4">
                            <div class="flex items-center justify-end gap-2">
                                <Link :href="route('clients.edit', client.id)">
                                    <Button variant="outline" size="icon" class="h-8 w-8">
                                        <FileEdit class="h-4 w-4" />
                                        <span class="sr-only">Editar</span>
                                    </Button>
                                </Link>
                                <Button
                                    variant="outline"
                                    size="icon"
                                    class="h-8 w-8 text-red-600 hover:text-red-700 hover:border-red-400 dark:hover:border-red-600"
                                    @click="deleteClient(client.id)"
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
                        colspan="7"
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