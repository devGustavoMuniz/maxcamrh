<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import ClientForm from "@/Components/Client/ClientForm.vue";

const props = defineProps({
    clientData: {
        type: Object,
        default: () => ({}),
    },
    franchises: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    _method: "PUT",
    name: props.clientData.data.user.name,
    email: props.clientData.data.user.email,
    password: "",
    password_confirmation: "",
    cnpj: props.clientData.data.cnpj,
    test_number: props.clientData.data.test_number,
    contract_end_date: props.clientData.data.contract_end_date_form,
    is_monthly_contract: props.clientData.data.is_monthly_contract,
    phone: props.clientData.data.phone,
    logo_file: null,
    franchise_id: props.clientData.data.franchise_id || null,
});

const submit = () => {
    form
        .transform((data) => ({
            ...data,
            cnpj: data.cnpj.replace(/\D/g, ""),
            phone: data.phone ? data.phone.replace(/\D/g, "") : null,
        }))
        .post(route("clients.update", props.clientData.data.id), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                form.reset("password", "password_confirmation");
                form.logo_file = null;
            },
        });
};
</script>

<template>
    <Head :title="'Editar Cliente: ' + clientData.data.user.name" />
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Editar Cliente: {{ clientData.data.user.name }}
            </h2>
        </template>

        <div class="mx-auto w-full">
            <div
                class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 md:p-8"
            >
                <ClientForm
                    :client-form="form"
                    :franchises="franchises"
                    :is-edit="true"
                    @submit="submit"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
