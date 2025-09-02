<script setup>
import { Link, usePage, useForm } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import {
    Select,
    SelectTrigger,
    SelectValue,
    SelectContent,
    SelectItem,
    SelectGroup,
} from "@/Components/ui/select";
import InputError from "@/Components/InputError.vue";
import { ref, computed } from "vue";

const props = defineProps({
    clientForm: {
        type: Object,
        default: () => ({}),
    },
    franchises: {
        type: Array,
        default: () => [],
    },
    isEdit: { type: Boolean, default: false },
});

const form = useForm({
    franchise_id: props.clientForm.franchise_id,
    name: props.clientForm.name,
    email: props.clientForm.email,
    cnpj: props.clientForm.cnpj,
    phone: props.clientForm.phone,
    test_number: props.clientForm.test_number,
    contract_end_date: props.clientForm.contract_end_date,
    logo_file: null,
    is_monthly_contract: props.clientForm.is_monthly_contract,
    password: props.clientForm.password,
    password_confirmation: props.clientForm.password_confirmation,
});

const emit = defineEmits(["submit"]);

const page = usePage();
const userRole = computed(() => page.props.auth.user?.role);

const logoFileInput = ref(null);
const logoPreview = ref(props.clientForm.logo_full_url || null);

function handleLogoUpload(event) {
    const file = event.target.files[0];
    if (file) {
        form.logo_file = file;
        logoPreview.value = URL.createObjectURL(file);
    }
}

const handleSubmit = () => {
    emit("submit", form);
};
</script>

<template>
    <form class="space-y-6" @submit.prevent="handleSubmit">
        <div class="space-y-4">
            <h3
                class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b dark:border-gray-700 pb-2"
            >
                Dados de Acesso
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div
                    v-if="
                        userRole === 'admin' &&
                        props.franchises &&
                        props.franchises.length > 0
                    "
                    class="md:col-span-2"
                >
                    <Label for="franchise_id">Franqueado Associado</Label>
                    <Select v-model="form.franchise_id">
                        <SelectTrigger id="franchise_id" class="bg-white">
                            <SelectValue placeholder="Selecione um franqueado" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem :value="null" class="cursor-pointer">
                                    Nenhum
                                </SelectItem>
                                <SelectItem
                                    v-for="franchise in props.franchises"
                                    :key="franchise.id"
                                    :value="franchise.id"
                                    class="cursor-pointer"
                                >
                                    {{ franchise.name }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                    <InputError class="mt-2" :message="form.errors.franchise_id" />
                </div>
                <div>
                    <Label for="user_name">Nome do Usuário</Label>
                    <Input
                        id="user_name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full bg-white"
                        required
                        autofocus
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>
                <div>
                    <Label for="user_email">Email do Usuário</Label>
                    <Input
                        id="user_email"
                        v-model="form.email"
                        type="email"
                        class="mt-1 block w-full bg-white"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>
                <div>
                    <Label for="client_cnpj">CNPJ</Label>
                    <Input
                        id="client_cnpj"
                        v-model="form.cnpj"
                        v-mask="'##.###.###/####-##'"
                        type="text"
                        class="mt-1 block w-full bg-white"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.cnpj" />
                </div>
                <div>
                    <Label for="client_phone">Telefone</Label>
                    <Input
                        id="client_phone"
                        v-model="form.phone"
                        v-mask="['(##) ####-####', '(##) #####-####']"
                        type="text"
                        class="mt-1 block w-full bg-white"
                    />
                    <InputError class="mt-2" :message="form.errors.phone" />
                </div>
                <div>
                    <Label for="client_test_number">Número de Teste</Label>
                    <Input
                        id="client_test_number"
                        v-model="form.test_number"
                        type="text"
                        class="mt-1 block w-full bg-white"
                    />
                    <InputError class="mt-2" :message="form.errors.test_number" />
                </div>
                <div>
                    <Label for="client_contract_end_date"
                        >Data Final do Contrato</Label
                    >
                    <Input
                        id="client_contract_end_date"
                        v-model="form.contract_end_date"
                        type="date"
                        class="mt-1 block w-full bg-white"
                    />
                    <InputError
                        class="mt-2"
                        :message="form.errors.contract_end_date"
                    />
                </div>
                <div>
                    <Label>Logo</Label>
                    <div class="mt-1 flex items-center gap-4">
                        <input
                            ref="logoFileInput"
                            type="file"
                            class="hidden"
                            accept="image/*"
                            @change="handleLogoUpload"
                        />
                        <Button
                            type="button"
                            variant="outline"
                            class="bg-white"
                            @click="logoFileInput?.click()"
                        >Escolher Arquivo</Button
                        >
                        <img
                            v-if="logoPreview"
                            :src="logoPreview"
                            alt="Preview Logo"
                            class="h-12 w-auto rounded-md object-cover"
                        />
                    </div>
                    <InputError class="mt-2" :message="form.errors.logo_file" />
                </div>
                <div class="flex items-center space-x-2 self-end pb-1">
                    <input
                        id="is_monthly_contract"
                        v-model="form.is_monthly_contract"
                        type="checkbox"
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                    />
                    <label
                        for="is_monthly_contract"
                        class="text-sm font-medium text-gray-700 dark:text-gray-300"
                        >Contrato Mensal?</label
                    >
                    <InputError
                        class="mt-2"
                        :message="form.errors.is_monthly_contract"
                    />
                </div>
            </div>
            <div v-if="!isEdit" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <Label for="user_password">Senha</Label>
                    <Input
                        id="user_password"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full bg-white"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>
                <div>
                    <Label for="user_password_confirmation"
                        >Confirmar Senha</Label
                    >
                    <Input
                        id="user_password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="mt-1 block w-full bg-white"
                        required
                    />
                    <InputError
                        class="mt-2"
                        :message="form.errors.password_confirmation"
                    />
                </div>
            </div>
        </div>

        <div
            class="flex items-center justify-end mt-6 pt-6 border-t dark:border-gray-700"
        >
            <Link :href="route('clients.index')" class="mr-4">
                <Button variant="outline" class="bg-white" type="button"
                    >Cancelar</Button
                >
            </Link>
            <Button
                type="submit"
                variant="black"
                class="bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600"
                :disabled="form.processing"
            >
                {{ isEdit ? 'Atualizar Cliente' : 'Criar Cliente' }}
            </Button>
        </div>
    </form>
</template>
