<script setup>
import { Link } from "@inertiajs/vue3";
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from "@/Components/ui/card";
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
    <Card class="bg-gray-100 dark:bg-gray-800 shadow-sm">
        <CardHeader class="flex flex-row items-center gap-4 space-y-0 pb-2">
            <Avatar class="h-12 w-12">
                <AvatarImage :src="client.logo_full_url" :alt="client.user.name" />
                <AvatarFallback>{{ client.user.name?.substring(0, 2).toUpperCase() || "C" }}</AvatarFallback>
            </Avatar>
            <div class="flex-1">
                <CardTitle class="text-lg text-gray-800 dark:text-gray-200">{{ client.user.name }}</CardTitle>
                <CardDescription class="text-gray-600 dark:text-gray-400">{{ client.user.email }}</CardDescription>
            </div>
        </CardHeader>
        <CardContent class="text-sm text-gray-700 dark:text-gray-300 space-y-1 pt-2 pb-4 px-6">
            <p>
                <strong class="font-medium text-gray-800 dark:text-gray-200">CNPJ:</strong>
                {{ client.cnpj }}
            </p>
            <p>
                <strong class="font-medium text-gray-800 dark:text-gray-200">Telefone:</strong>
                {{ client.phone || "Indisponível" }}
            </p>
            <p v-if="client.franchise_name">
                <strong class="font-medium text-gray-800 dark:text-gray-200">Franqueado:</strong>
                {{ client.franchise_name }}
            </p>
        </CardContent>
        <CardFooter class="flex justify-end gap-2 bg-gray-200/50 dark:bg-gray-900/50 py-3 px-6">
            <Link :href="route('clients.edit', client.id)">
                <Button variant="outline" size="sm">
                    <FileEdit class="h-4 w-4 mr-2" />
                    Editar
                </Button>
            </Link>
            <Button
                variant="outline"
                size="sm"
                class="text-red-600 hover:text-red-700 hover:border-red-400 dark:hover:border-red-600"
                @click="handleDelete"
            >
                <Trash2 class="h-4 w-4 mr-2" />
                Excluir
            </Button>
        </CardFooter>
    </Card>
</template>
