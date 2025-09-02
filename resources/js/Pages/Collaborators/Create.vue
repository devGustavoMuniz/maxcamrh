<script setup>
 
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import CollaboratorFormStep1 from "@/Components/Collaborator/CollaboratorFormStep1.vue";
import CollaboratorFormStep2 from "@/Components/Collaborator/CollaboratorFormStep2.vue";
import CollaboratorFormStep3 from "@/Components/Collaborator/CollaboratorFormStep3.vue";
import CollaboratorFormStep4 from "@/Components/Collaborator/CollaboratorFormStep4.vue";
import CollaboratorFormNavigation from "@/Components/Collaborator/CollaboratorFormNavigation.vue";

const props = defineProps({
    clients: {
        type: Array,
        default: () => [],
    },
});

const currentStep = ref(1);
const totalSteps = 4;
const stepNames = [
    "Dados Pessoais",
    "Contato e Endereço",
    "Dados Contratuais",
    "Documentos e Bancários",
];

const nextStep = () => {
    if (currentStep.value < totalSteps) {
        currentStep.value++;
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

const goToStep = (step) => {
    currentStep.value = step;
};

const form = useForm({
    user: { name: "", email: "", password: "", password_confirmation: "" },
    collaborator: {
        client_id: null,
        photo_file: null,
        curriculum_file: null,
        date_of_birth: "",
        gender: "",
        is_special_needs_person: false,
        marital_status: "",
        scholarity: "",
        father_name: "",
        mother_name: "",
        nationality: "",
        personal_email: "",
        business_email: "",
        phone: "",
        cellphone: "",
        emergency_phone: "",
        department: "",
        position: "",
        type_of_contract: "",
        salary: null,
        admission_date: "",
        direct_superior_name: "",
        hierarchical_degree: "",
        observations: "",
        contract_start_date: "",
        contract_expiration: "",
        cpf: "",
        rg: "",
        cnh: "",
        reservista: "",
        titulo_eleitor: "",
        zona_eleitoral: "",
        pis_ctps_numero: "",
        ctps_serie: "",
        banco: "",
        agencia: "",
        conta_corrente: "",
    },
    address: {
        cep: "",
        street: "",
        number: "",
        complement: "",
        neighborhood: "",
        state: "",
        city: "",
    },
});

const submit = () => {
    form.post(route("collaborators.store"), {
        onSuccess: () => {
            form.reset();
            currentStep.value = 1;
        },
    });
};
</script>

<template>
    <Head title="Adicionar Colaborador" />
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Adicionar Novo Colaborador (Etapa {{ currentStep }} de {{ totalSteps }})
            </h2>
        </template>

        
        <div class="mx-auto w-full">
            <div
                class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4 md:p-8"
            >
                <form class="space-y-6" @submit.prevent="submit">
                    <CollaboratorFormStep1 :collaborator-form="form" :clients="clients" />
                    <CollaboratorFormStep2 v-if="currentStep === 2" :collaborator-form="form" />
                    <CollaboratorFormStep3 v-if="currentStep === 3" :collaborator-form="form" />

                    <CollaboratorFormStep4 v-if="currentStep === 4" :collaborator-form="form" />

                    <CollaboratorFormNavigation
                        :current-step="currentStep"
                        :total-steps="totalSteps"
                        :step-names="stepNames"
                        :form-processing="form.processing"
                        @next="nextStep"
                        @prev="prevStep"
                        @go-to-step="goToStep"
                        @submit="submit"
                    />

                    
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
