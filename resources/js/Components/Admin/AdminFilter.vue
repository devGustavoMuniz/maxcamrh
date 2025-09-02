<script setup>
import { ref, watch } from "vue";
import debounce from "lodash/debounce";
import { Input } from "@/Components/ui/input";
import { Button } from "@/Components/ui/button";
import { Search, UserPlus } from "lucide-vue-next";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    search: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["update:search"]);

const localSearch = ref(props.search);

watch(
    localSearch,
    debounce((value) => {
        emit("update:search", value);
    }, 300),
);
</script>

<template>
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-4">
        <div class="relative w-full md:max-w-xs">
            <Input
                v-model="localSearch"
                type="text"
                placeholder="Buscar por nome ou email..."
                class="pl-10 w-full bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600"
            />
            <div
                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
            >
                <Search class="h-5 w-5 text-gray-400" />
            </div>
        </div>
        <Link :href="route('admins.create')" class="w-full md:w-auto">
            <Button
                variant="black"
                class="w-full bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600"
            >
                <UserPlus class="h-4 w-4 mr-2" />
                Adicionar Administrador
            </Button>
        </Link>
    </div>
</template>
