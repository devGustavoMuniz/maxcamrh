<script setup>
import { ref, watch, computed } from "vue";
import debounce from "lodash/debounce";
import { Input } from "@/Components/ui/input";
import { Button } from "@/Components/ui/button";
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandList, CommandItem } from "@/Components/ui/command";
import { Popover, PopoverContent, PopoverTrigger } from "@/Components/ui/popover";
import { Search, ChevronsUpDown, Check, Plus } from "lucide-vue-next";
import { cn } from "@/lib/utils";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    search: {
        type: String,
        default: "",
    },
    franchises: {
        type: Array,
        default: () => [],
    },
    selectedFranchise: {
        type: [String, Number, null],
        default: null,
    },
    userRole: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["update:search", "update:selectedFranchise"]);

const localSearch = ref(props.search);
const localSelectedFranchise = ref(props.selectedFranchise);
const openFranchiseCombobox = ref(false);

const currentFranchiseLabel = computed(() => {
    const franchise = props.franchises.find(
        (f) => String(f.id) === String(localSelectedFranchise.value),
    );
    return franchise ? franchise.name : "Filtrar por franqueado...";
});

watch(
    localSearch,
    debounce((value) => {
        emit("update:search", value);
    }, 300),
);

watch(
    localSelectedFranchise,
    debounce((value) => {
        emit("update:selectedFranchise", value);
    }, 300),
);
</script>

<template>
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-4">
        <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
            <div class="relative w-full sm:max-w-xs">
                <Input
                    v-model="localSearch"
                    type="text"
                    placeholder="Buscar por nome ou email"
                    class="pl-10 w-full bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600"
                />
                <div
                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                >
                    <Search class="h-5 w-5 text-gray-400" />
                </div>
            </div>

            <div v-if="userRole === 'admin'" class="w-full sm:w-auto">
                <Popover v-model:open="openFranchiseCombobox">
                    <PopoverTrigger as-child>
                        <Button
                            variant="outline"
                            role="combobox"
                            :aria-expanded="openFranchiseCombobox"
                            class="w-full sm:w-[280px] justify-between bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600"
                        >
                            {{
                                localSelectedFranchise
                                    ? currentFranchiseLabel
                                    : "Filtrar por franqueado..."
                            }}
                            <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                        </Button>
                    </PopoverTrigger>
                    <PopoverContent class="w-full sm:w-[280px] p-0">
                        <Command>
                            <CommandInput
                                class="h-4 my-2"
                                placeholder="Buscar franqueado..."
                            />
                            <CommandList>
                                <CommandEmpty>Nenhum franqueado encontrado.</CommandEmpty>
                                <CommandGroup>
                                    <CommandItem
                                        :value="null"
                                        @select="
                                            () => {
                                                localSelectedFranchise = null;
                                                openFranchiseCombobox = false;
                                            }
                                        "
                                    >
                                        <Check
                                            :class="
                                                cn(
                                                    'mr-2 h-4 w-4',
                                                    localSelectedFranchise === null
                                                        ? 'opacity-100'
                                                        : 'opacity-0',
                                                )
                                            "
                                        />
                                        Todos os Franqueados
                                    </CommandItem>
                                    <CommandItem
                                        v-for="franchise in franchises"
                                        :key="franchise.id"
                                        :value="franchise.name"
                                        @select="
                                            () => {
                                                localSelectedFranchise = String(franchise.id);
                                                openFranchiseCombobox = false;
                                            }
                                        "
                                    >
                                        <Check
                                            :class="
                                                cn(
                                                    'mr-2 h-4 w-4',
                                                    localSelectedFranchise === String(franchise.id)
                                                        ? 'opacity-100'
                                                        : 'opacity-0',
                                                )
                                            "
                                        />
                                        {{ franchise.name }}
                                    </CommandItem>
                                </CommandGroup>
                            </CommandList>
                        </Command>
                    </PopoverContent>
                </Popover>
            </div>
        </div>
        <Link :href="route('clients.create')" class="w-full md:w-auto">
            <Button
                variant="black"
                class="w-full bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600"
            >
                <Plus class="h-4 w-4 mr-2" />
                Adicionar Cliente
            </Button>
        </Link>
    </div>
</template>
