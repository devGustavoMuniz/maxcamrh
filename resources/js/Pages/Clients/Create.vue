<script setup>
 
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import ClientForm from "@/Components/Client/ClientForm.vue";

const props = defineProps({
    franchises: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    cnpj: "",
    test_number: "",
    contract_end_date: "",
    is_monthly_contract: false,
    phone: "",
    logo_file: null,
    franchise_id: null,
});

const submit = () => {
    console.log("Dados a serem enviados:", form.data());

    form
        .transform((data) => ({
            ...data,
            cnpj: data.cnpj.replace(/\D/g, ""),
            phone: data.phone ? data.phone.replace(/\D/g, "") : null,
        }))
        .post(route("clients.store"), {
            onSuccess: () => form.reset(),
        });
};
</script>

<template>
    <Head title="Adicionar Cliente" />
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Adicionar Novo Cliente
            </h2>
        </template>

        <div class="mx-auto w-full">
            <div
                class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 md:p-8"
            >
                <ClientForm :client-form="form" :franchises="franchises" @submit="submit" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
