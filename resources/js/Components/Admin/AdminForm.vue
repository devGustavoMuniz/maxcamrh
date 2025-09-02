<script setup>
import { Link } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    adminForm: {
        type: Object,
        default: () => ({}),
    },
    isEdit: { type: Boolean, default: false },
});

const form = props.adminForm;

const emit = defineEmits(["submit"]);

const handleSubmit = () => {
    emit("submit");
};
</script>

<template>
    <form class="space-y-6" @submit.prevent="handleSubmit">
        <h3
            class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b dark:border-gray-700 pb-2"
        >
            {{ isEdit ? 'Dados do Usuário' : 'Dados de Acesso do Administrador' }}
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <Label for="name">Nome</Label>
                <Input
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full bg-white"
                    required
                    autofocus
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <Label for="email">Email</Label>
                <Input
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full bg-white"
                    required
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>
        </div>

        <div v-if="isEdit" class="space-y-4">
            <h3
                class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b dark:border-gray-700 pb-2"
            >
                Alterar Senha
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 -mt-2">
                Deixe os campos abaixo em branco para não modificar a senha atual.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <Label for="password">Nova Senha</Label>
                    <Input
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full bg-white"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div>
                    <Label for="password_confirmation">Confirmar Nova Senha</Label>
                    <Input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="mt-1 block w-full bg-white"
                    />
                    <InputError
                        class="mt-2"
                        :message="form.errors.password_confirmation"
                    />
                </div>
            </div>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <Label for="password">Senha</Label>
                <Input
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full bg-white"
                    required
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <Label for="password_confirmation">Confirmar Senha</Label>
                <Input
                    id="password_confirmation"
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

        <div
            class="flex items-center justify-end mt-6 pt-6 border-t dark:border-gray-700"
        >
            <Link :href="route('admins.index')" class="mr-4">
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
                {{ isEdit ? 'Atualizar Administrador' : 'Salvar Administrador' }}
            </Button>
        </div>
    </form>
</template>
