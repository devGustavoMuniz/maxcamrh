<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import AdminForm from "@/Components/Admin/AdminForm.vue";

const props = defineProps({
    admin_user: {
        type: Object,
        default: () => ({}),
    },
});


const form = useForm({
    _method: "PUT",
    name: props.admin_user?.name || "",
    email: props.admin_user?.email || "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("admins.update", props.admin_user?.id), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <Head :title="'Editar Administrador: ' + (admin_user?.name || '')" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Editar Administrador: {{ admin_user?.name || 'Carregando...' }}
            </h2>
        </template>

        <div class="mx-auto w-full">
            <div
                class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 md:p-8"
            >
                <AdminForm :admin-form="form" :is-edit="true" @submit="submit" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
