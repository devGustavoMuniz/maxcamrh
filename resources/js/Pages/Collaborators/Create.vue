<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import CollaboratorStepTabs from "@/Components/Collaborator/CollaboratorStepTabs.vue";
import CollaboratorFormActions from "@/Components/Collaborator/CollaboratorFormActions.vue";
import CollaboratorFormStep1 from "@/Components/Collaborator/CollaboratorFormStep1.vue";
import CollaboratorFormStep2 from "@/Components/Collaborator/CollaboratorFormStep2.vue";
import CollaboratorFormStep3 from "@/Components/Collaborator/CollaboratorFormStep3.vue";
import CollaboratorFormStep4 from "@/Components/Collaborator/CollaboratorFormStep4.vue";

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

const getStepWithErrors = (errors) => {
    // Etapa 1: Dados Pessoais
    const step1Fields = ['user.name', 'user.email', 'user.password', 'user.password_confirmation', 'collaborator.client_id', 'collaborator.date_of_birth', 'collaborator.gender', 'collaborator.marital_status', 'collaborator.scholarity', 'collaborator.father_name', 'collaborator.mother_name', 'collaborator.nationality', 'collaborator.is_special_needs_person'];
    
    // Etapa 2: Contato e Endereço
    const step2Fields = ['collaborator.personal_email', 'collaborator.business_email', 'collaborator.phone', 'collaborator.cellphone', 'collaborator.emergency_phone', 'address.cep', 'address.street', 'address.number', 'address.complement', 'address.neighborhood', 'address.city', 'address.state'];
    
    // Etapa 3: Dados Contratuais
    const step3Fields = ['collaborator.department', 'collaborator.position', 'collaborator.type_of_contract', 'collaborator.salary', 'collaborator.admission_date', 'collaborator.contract_start_date', 'collaborator.contract_expiration', 'collaborator.direct_superior_name', 'collaborator.hierarchical_degree', 'collaborator.observations'];
    
    // Etapa 4: Documentos e Bancários
    const step4Fields = ['collaborator.cpf', 'collaborator.rg', 'collaborator.cnh', 'collaborator.reservista', 'collaborator.titulo_eleitor', 'collaborator.zona_eleitoral', 'collaborator.pis_ctps_numero', 'collaborator.ctps_serie', 'collaborator.photo_file', 'collaborator.curriculum_file', 'collaborator.banco', 'collaborator.agencia', 'collaborator.conta_corrente'];
    
    // Verifica cada etapa em ordem
    if (step1Fields.some(field => errors[field])) return 1;
    if (step2Fields.some(field => errors[field])) return 2;
    if (step3Fields.some(field => errors[field])) return 3;
    if (step4Fields.some(field => errors[field])) return 4;
    
    return currentStep.value; // Mantém a etapa atual se não houver erros específicos
};

const submit = () => {
    form.post(route("collaborators.store"), {
        onSuccess: () => {
            form.reset();
            currentStep.value = 1;
        },
        onError: (errors) => {
            // Navega para a primeira etapa que contém erros
            const stepWithError = getStepWithErrors(errors);
            if (stepWithError !== currentStep.value) {
                currentStep.value = stepWithError;
            }
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
                <CollaboratorStepTabs
                    :current-step="currentStep"
                    :step-names="stepNames"
                    @go-to-step="goToStep"
                />

                <form class="space-y-6" @submit.prevent="submit">
                    <CollaboratorFormStep1 v-if="currentStep === 1" :collaborator-form="form" :clients="clients" />
                    <CollaboratorFormStep2 v-if="currentStep === 2" :collaborator-form="form" />
                    <CollaboratorFormStep3 v-if="currentStep === 3" :collaborator-form="form" />
                    <CollaboratorFormStep4 v-if="currentStep === 4" :collaborator-form="form" />

                    <CollaboratorFormActions
                        :current-step="currentStep"
                        :total-steps="totalSteps"
                        :form-processing="form.processing"
                        @next="nextStep"
                        @prev="prevStep"
                        @submit="submit"
                    />
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
