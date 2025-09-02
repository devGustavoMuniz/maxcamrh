<script setup>
import { Link } from "@inertiajs/vue3";
import { TableCell, TableRow } from "@/Components/ui/table";
import { Avatar, AvatarFallback, AvatarImage } from "@/Components/ui/avatar";
import { Button } from "@/Components/ui/button";
import { FileEdit, Trash2 } from "lucide-vue-next";

const props = defineProps({
    client: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(["delete"]);

const handleDelete = () => {
    emit("delete", props.client.id);
};
</script>

<template>
    <TableRow class="[&>td]:py-2 border-b dark:border-gray-700">
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
                    @click="handleDelete"
                >
                    <Trash2 class="h-4 w-4" />
                    <span class="sr-only">Excluir</span>
                </Button>
            </div>
        </TableCell>
    </TableRow>
</template>
