<script setup>
 
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
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
                <form class="space-y-6" @submit.prevent="submit">
                    <ClientForm :client-form="form" :franchises="franchises" />

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
                            Criar Cliente
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
