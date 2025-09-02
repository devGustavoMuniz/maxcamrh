<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import FranchiseForm from "@/Components/Franchise/FranchiseForm.vue";

const props = defineProps({
    franchiseData: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
    _method: "PUT",
    name: props.franchiseData.data.user.name,
    email: props.franchiseData.data.user.email,
    password: "",
    password_confirmation: "",
    maxcam_email: props.franchiseData.data.maxcam_email,
    cnpj: props.franchiseData.data.cnpj,
    max_client: props.franchiseData.data.max_client,
    contract_start_date: props.franchiseData.data.contract_start_date_form,
    actuation_region: props.franchiseData.data.actuation_region,
    document_file: null,
    observations: props.franchiseData.data.observations,
});

const submit = () => {
    form
        .transform((data) => ({
            ...data,
            cnpj: data.cnpj.replace(/\D/g, ""),
        }))
        .post(route("franchises.update", props.franchiseData.data.id), {
            onSuccess: () => {
                form.reset("password", "password_confirmation");
                form.document_file = null;
            },
        });
};
</script>

<template>
    <Head :title="'Editar Franqueado: ' + franchiseData.data.user.name" />
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Editar Franqueado: {{ franchiseData.data.user.name }}
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
                        :initial-document-url="franchiseData.data.document_full_url"
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
