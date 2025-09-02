<script setup>
import { computed } from "vue";
import { usePage, useForm } from "@inertiajs/vue3";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
    SelectGroup,
} from "@/Components/ui/select";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    collaboratorForm: {
        type: Object,
        default: () => ({}),
    },
    clients: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    user: {
        name: props.collaboratorForm.user.name,
        email: props.collaboratorForm.user.email,
        password: props.collaboratorForm.user.password,
        password_confirmation: props.collaboratorForm.user.password_confirmation,
    },
    collaborator: {
        client_id: props.collaboratorForm.collaborator.client_id,
        date_of_birth: props.collaboratorForm.collaborator.date_of_birth,
        gender: props.collaboratorForm.collaborator.gender,
        marital_status: props.collaboratorForm.collaborator.marital_status,
        scholarity: props.collaboratorForm.collaborator.scholarity,
        father_name: props.collaboratorForm.collaborator.father_name,
        mother_name: props.collaboratorForm.collaborator.mother_name,
        nationality: props.collaboratorForm.collaborator.nationality,
        is_special_needs_person: props.collaboratorForm.collaborator.is_special_needs_person,
    },
});

const page = usePage();
const userRole = computed(() => page.props.auth.user?.role);
</script>

<template>
    <section class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div
                v-if="
                    (userRole === 'admin' || userRole === 'franchise') &&
                    props.clients &&
                    props.clients.length > 0
                "
                class="md:col-span-2"
            >
                <Label for="collaborator_client_id">Cliente Associado</Label>
                <Select v-model="form.collaborator.client_id">
                    <SelectTrigger
                        id="collaborator_client_id"
                        class="bg-white dark:bg-gray-700"
                    >
                        <SelectValue placeholder="Selecione um cliente" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectItem :value="null" class="cursor-pointer">
                                Nenhum
                            </SelectItem>
                            <SelectItem
                                v-for="client in clients"
                                :key="client.id"
                                class="cursor-pointer"
                                :value="client.id"
                            >{{ client.name }}
                            </SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.client_id']"
                />
            </div>
            <div v-if="userRole === 'client'" class="md:col-span-2">
                <p
                    class="p-3 bg-blue-50 dark:bg-blue-900/50 border border-blue-200 dark:border-blue-700 rounded-md text-sm text-blue-700 dark:text-blue-300"
                >
                    Este colaborador será associado automaticamente ao seu perfil
                    de cliente.
                </p>
            </div>
            <div>
                <Label for="user_name">Nome Completo</Label>
                <Input
                    id="user_name"
                    v-model="form.user.name"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                    required
                />
                <InputError class="mt-2" :message="form.errors['user.name']" />
            </div>
            <div>
                <Label for="user_email">Email de Acesso</Label>
                <Input
                    id="user_email"
                    v-model="form.user.email"
                    type="email"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                    required
                />
                <InputError class="mt-2" :message="form.errors['user.email']" />
            </div>
            <div>
                <Label for="user_password">Senha</Label>
                <Input
                    id="user_password"
                    v-model="form.user.password"
                    type="password"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                    required
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['user.password']"
                />
            </div>
            <div>
                <Label for="user_password_confirmation">Confirmar Senha</Label>
                <Input
                    id="user_password_confirmation"
                    v-model="form.user.password_confirmation"
                    type="password"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                    required
                />
            </div>
            <div>
                <Label for="collaborator_date_of_birth"
                    >Data de Nascimento</Label
                >
                <Input
                    id="collaborator_date_of_birth"
                    v-model="form.collaborator.date_of_birth"
                    type="date"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.date_of_birth']"
                />
            </div>
            <div>
                <Label for="collaborator_gender">Gênero</Label>
                <Select v-model="form.collaborator.gender">
                    <SelectTrigger class="bg-white dark:bg-gray-700">
                        <SelectValue placeholder="Selecione..." />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="Masculino">Masculino</SelectItem>
                        <SelectItem value="Feminino">Feminino</SelectItem>
                        <SelectItem value="Outro">Outro</SelectItem>
                        <SelectItem value="Não Informado">Não Informar</SelectItem>
                    </SelectContent>
                </Select>
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.gender']"
                />
            </div>
            <div>
                <Label for="collaborator_marital_status">Estado Civil</Label>
                <Select v-model="form.collaborator.marital_status">
                    <SelectTrigger class="bg-white dark:bg-gray-700">
                        <SelectValue placeholder="Selecione..." />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="Solteiro(a)">Solteiro(a)</SelectItem>
                        <SelectItem value="Casado(a)">Casado(a)</SelectItem>
                        <SelectItem value="Divorciado(a)">Divorciado(a)</SelectItem>
                        <SelectItem value="Viúvo(a)">Viúvo(a)</SelectItem>
                        <SelectItem value="União Estável">União Estável</SelectItem>
                    </SelectContent>
                </Select>
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.marital_status']"
                />
            </div>
            <div>
                <Label for="collaborator_scholarity">Escolaridade</Label>
                <Input
                    id="collaborator_scholarity"
                    v-model="form.collaborator.scholarity"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.scholarity']"
                />
            </div>
            <div>
                <Label for="collaborator_father_name">Nome do Pai</Label>
                <Input
                    id="collaborator_father_name"
                    v-model="form.collaborator.father_name"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.father_name']"
                />
            </div>
            <div>
                <Label for="collaborator_mother_name">Nome da Mãe</Label>
                <Input
                    id="collaborator_mother_name"
                    v-model="form.collaborator.mother_name"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.mother_name']"
                />
            </div>
            <div>
                <Label for="collaborator_nationality">Nacionalidade</Label>
                <Input
                    id="collaborator_nationality"
                    v-model="form.collaborator.nationality"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.nationality']"
                />
            </div>
            <div class="flex items-center space-x-2 self-end pb-1">
                <input
                    id="collaborator_is_special_needs_person"
                    v-model="form.collaborator.is_special_needs_person"
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                />
                <label
                    for="collaborator_is_special_needs_person"
                    class="text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                    Pessoa com Necessidades Especiais?
                </label>
                <InputError
                    class="mt-2"
                    :message="
                        form.errors['collaborator.is_special_needs_person']
                    "
                />
            </div>
        </div>
    </section>
</template>
