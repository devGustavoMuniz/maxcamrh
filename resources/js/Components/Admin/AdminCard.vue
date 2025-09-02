<script setup>
import { Link } from "@inertiajs/vue3";
import { Card, CardHeader, CardTitle, CardDescription, CardFooter } from "@/Components/ui/card";
import { Button } from "@/Components/ui/button";
import { FileEdit, Trash2 } from "lucide-vue-next";

const props = defineProps({
    admin: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(["delete"]);

const handleDelete = () => {
    emit("delete", props.admin.id);
};
</script>

<template>
    <Card class="bg-gray-100 dark:bg-gray-800 shadow-sm">
        <CardHeader>
            <CardTitle class="text-lg text-gray-800 dark:text-gray-200">{{ admin.name }}</CardTitle>
            <CardDescription class="text-gray-600 dark:text-gray-400">{{ admin.email }}</CardDescription>
        </CardHeader>
        <CardFooter class="flex justify-end gap-2">
            <Link :href="route('admins.edit', admin.id)">
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
