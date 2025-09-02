<script setup>
import { Link } from "@inertiajs/vue3";
import {
    Card,
    CardHeader,
    CardTitle,
    CardDescription,
    CardFooter,
} from "@/Components/ui/card";
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
                        class="text-red-600 hover:text-red-700 hover:border-red-400 dark:hover:border-red-600"
                        @click="deleteFranchise(franchise.id)"
                    >
                        <Trash2 class="h-4 w-4 mr-2" />
                        Excluir
                    </Button>
                </CardFooter>
            </Card>
        </template>
        <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
            Nenhum franqueado cadastrado.
        </div>
    </div>
</template>
