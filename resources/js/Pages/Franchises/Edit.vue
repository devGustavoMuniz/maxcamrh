<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import FranchiseForm from "@/Components/Franchise/FranchiseForm.vue";

const props = defineProps({
    franchise_data: {
        type: Object,
        default: () => ({}),
    },
});


const form = useForm({
    _method: "PUT",
    name: props.franchise_data?.user?.data?.name || "",
    email: props.franchise_data?.user?.data?.email || "",
    password: "",
    password_confirmation: "",
    maxcam_email: props.franchise_data?.maxcam_email || "",
    cnpj: props.franchise_data?.cnpj || "",
    max_client: props.franchise_data?.max_client || 0,
    contract_start_date: props.franchise_data?.contract_start_date_form || "",
    actuation_region: props.franchise_data?.actuation_region || "",
    document_file: null,
    observations: props.franchise_data?.observations || "",
});

const submit = () => {
    form
        .transform((data) => ({
            ...data,
            cnpj: data.cnpj.replace(/\D/g, ""),
        }))
        .post(route("franchises.update", props.franchise_data?.id), {
            onSuccess: () => {
                form.reset("password", "password_confirmation");
                form.document_file = null;
            },
        });
};
</script>

<template>
    <Head :title="'Editar Franqueado: ' + (franchise_data?.user?.data?.name || '')" />
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Editar Franqueado: {{ franchise_data?.user?.data?.name || 'Carregando...' }}
            </h2>
        </template>

        <div class="mx-auto w-full">
            <div
                class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 md:p-8"
            >
                <form class="space-y-6" @submit.prevent="submit">
                    <FranchiseForm
                        :franchise-form="form"
                        :is-edit="true"
                        :initial-document-url="franchise_data?.document_full_url"
                    />

                    <div
                        class="flex items-center justify-end mt-6 pt-6 border-t dark:border-gray-700"
                    >
                        <Link :href="route('franchises.index')" class="mr-4">
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
                            Atualizar Franqueado
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
