<script setup>
import { ref, watch, computed } from "vue";
import debounce from "lodash/debounce";
import { Input } from "@/Components/ui/input";
import { Button } from "@/Components/ui/button";
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from "@/Components/ui/command";
import { Popover, PopoverContent, PopoverTrigger } from "@/Components/ui/popover";
import { Search, ChevronsUpDown, Check, UserPlus } from "lucide-vue-next";
import { cn } from "@/lib/utils";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    search: {
        type: String,
        default: "",
    },
    clients: {
        type: Array,
        default: () => [],
    },
    selectedClient: {
        type: [String, Number, null],
        default: null,
    },
    userRole: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["update:search", "update:selectedClient"]);

const localSearch = ref(props.search);
const localSelectedClient = ref(props.selectedClient);
const openClientCombobox = ref(false);

const currentClientLabel = computed(() => {
    const client = props.clients.find(
        (c) => String(c.id) === String(localSelectedClient.value),
    );
    return client ? client.name : "Filtrar por cliente...";
});

watch(
    localSearch,
    debounce((value) => {
        emit("update:search", value);
    }, 300),
);

watch(
    localSelectedClient,
    debounce((value) => {
        emit("update:selectedClient", value);
    }, 300),
);
</script>

<template>
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-4">
        <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
            <div class="relative w-full">
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
            <div
                v-if="userRole === 'admin' || userRole === 'franchise'"
                class="w-full sm:w-auto"
            >
                <Popover v-model:open="openClientCombobox">
                    <PopoverTrigger as-child>
                        <Button
                            variant="outline"
                            role="combobox"
                            :aria-expanded="openClientCombobox"
                            class="w-full sm:w-[280px] justify-between bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600"
                        >
                            {{
                                localSelectedClient
                                    ? currentClientLabel
                                    : "Filtrar por cliente..."
                            }}
                            <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                        </Button>
                    </PopoverTrigger>
                    <PopoverContent class="w-full sm:w-[280px] p-0">
                        <Command>
                            <CommandInput
                                class="h-4 my-2"
                                placeholder="Buscar cliente..."
                            />
                            <CommandList>
                                <CommandEmpty>Nenhum cliente encontrado.</CommandEmpty>
                                <CommandGroup>
                                    <CommandItem
                                        :value="null"
                                        @select="
                                            () => {
                                                localSelectedClient = null;
                                                openClientCombobox = false;
                                            }
                                        "
                                    >
                                        <Check
                                            :class="
                                                cn(
                                                    'mr-2 h-4 w-4',
                                                    localSelectedClient === null
                                                        ? 'opacity-100'
                                                        : 'opacity-0',
                                                )
                                            "
                                        />
                                        Todos os Clientes
                                    </CommandItem>
                                    <CommandItem
                                        v-for="client in clients"
                                        :key="client.id"
                                        :value="client.name"
                                        @select="
                                            () => {
                                                localSelectedClient = String(client.id);
                                                openClientCombobox = false;
                                            }
                                        "
                                    >
                                        <Check
                                            :class="
                                                cn(
                                                    'mr-2 h-4 w-4',
                                                    localSelectedClient === String(client.id)
                                                        ? 'opacity-100'
                                                        : 'opacity-0',
                                                )
                                            "
                                        />
                                        {{ client.name }}
                                    </CommandItem>
                                </CommandGroup>
                            </CommandList>
                        </Command>
                    </PopoverContent>
                </Popover>
            </div>
        </div>

        <Link :href="route('collaborators.create')" class="w-full md:w-auto">
            <Button
                variant="black"
                class="w-full bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600"
            >
                <UserPlus class="h-4 w-4 mr-2" />
                Adicionar Colaborador
            </Button>
        </Link>
    </div>
</template>
