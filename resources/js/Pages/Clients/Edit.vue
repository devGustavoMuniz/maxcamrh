<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import ClientForm from "@/Components/Client/ClientForm.vue";

const props = defineProps({
    client_data: {
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
    name: props.client_data?.user?.data?.name || "",
    email: props.client_data?.user?.data?.email || "",
    password: "",
    password_confirmation: "",
    cnpj: props.client_data?.cnpj || "",
    test_number: props.client_data?.test_number || "",
    contract_end_date: props.client_data?.contract_end_date_form || "",
    is_monthly_contract: props.client_data?.is_monthly_contract || false,
    phone: props.client_data?.phone || "",
    logo_file: null,
    logo_full_url: props.client_data?.logo_full_url || null,
    franchise_id: props.client_data?.franchise_id || null,
});

const submit = () => {
    form
        .transform((data) => ({
            ...data,
            cnpj: data.cnpj.replace(/\D/g, ""),
            phone: data.phone ? data.phone.replace(/\D/g, "") : null,
        }))
        .post(route("clients.update", props.client_data.id), {
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
    <Head :title="'Editar Cliente: ' + (client_data?.user?.data?.name || '')" />
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Editar Cliente: {{ client_data?.user?.data?.name || 'Carregando...' }}
            </h2>
        </template>

        <div class="mx-auto w-full">
            <div
                class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 md:p-8"
            >
                <form class="space-y-6" @submit.prevent="submit">
                    <ClientForm
                        :client-form="form"
                        :franchises="franchises"
                        :is-edit="true"
                    />

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
                            Atualizar Cliente
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
