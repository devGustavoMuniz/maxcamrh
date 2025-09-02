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
    collaborator_data: {
        type: Object,
        default: () => ({}),
    },
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
    _method: "PUT",
    user: {
        name: props.collaborator_data?.user?.data?.name || "",
        email: props.collaborator_data?.user?.data?.email || "",
        password: "",
        password_confirmation: "",
    },
    collaborator: {
        client_id: props.collaborator_data?.client_id || null,
        photo_file: null,
        curriculum_file: null,
        date_of_birth: props.collaborator_data?.date_of_birth || "",
        gender: props.collaborator_data?.gender || "",
        is_special_needs_person:
            props.collaborator_data?.is_special_needs_person || false,
        marital_status: props.collaborator_data?.marital_status || "",
        scholarity: props.collaborator_data?.scholarity || "",
        father_name: props.collaborator_data?.father_name || "",
        mother_name: props.collaborator_data?.mother_name || "",
        nationality: props.collaborator_data?.nationality || "",
        personal_email: props.collaborator_data?.personal_email || "",
        business_email: props.collaborator_data?.business_email || "",
        phone: props.collaborator_data?.phone || "",
        cellphone: props.collaborator_data?.cellphone || "",
        emergency_phone: props.collaborator_data?.emergency_phone || "",
        department: props.collaborator_data?.department || "",
        position: props.collaborator_data?.position || "",
        type_of_contract: props.collaborator_data?.type_of_contract || "",
        salary: props.collaborator_data?.salary || null,
        admission_date: props.collaborator_data?.admission_date || "",
        direct_superior_name:
            props.collaborator_data?.direct_superior_name || "",
        hierarchical_degree: props.collaborator_data?.hierarchical_degree || "",
        observations: props.collaborator_data?.observations || "",
        contract_start_date: props.collaborator_data?.contract_start_date || "",
        contract_expiration: props.collaborator_data?.contract_expiration || "",
        cpf: props.collaborator_data?.cpf || "",
        rg: props.collaborator_data?.rg || "",
        cnh: props.collaborator_data?.cnh || "",
        reservista: props.collaborator_data?.reservista || "",
        titulo_eleitor: props.collaborator_data?.titulo_eleitor || "",
        zona_eleitoral: props.collaborator_data?.zona_eleitoral || "",
        pis_ctps_numero: props.collaborator_data?.pis_ctps_numero || "",
        ctps_serie: props.collaborator_data?.ctps_serie || "",
        banco: props.collaborator_data?.banco || "",
        agencia: props.collaborator_data?.agencia || "",
        conta_corrente: props.collaborator_data?.conta_corrente || "",
    },
    address: {
        cep: props.collaborator_data?.address?.data?.cep || "",
        street: props.collaborator_data?.address?.data?.street || "",
        number: props.collaborator_data?.address?.data?.number || "",
        complement: props.collaborator_data?.address?.data?.complement || "",
        neighborhood: props.collaborator_data?.address?.data?.neighborhood || "",
        state: props.collaborator_data?.address?.data?.state || "",
        city: props.collaborator_data?.address?.data?.city || "",
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
    form.post(route("collaborators.update", props.collaborator_data.id), {
        onSuccess: () => {
            form.reset("user.password", "user.password_confirmation");
            form.collaborator.photo_file = null;
            form.collaborator.curriculum_file = null;
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
    <Head :title="'Editar Colaborador: ' + (collaborator_data?.user?.data?.name || '')" />
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Editar Colaborador: {{ collaborator_data?.user?.data?.name || 'Carregando...' }} (Etapa
                {{ currentStep }})
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
                    <CollaboratorFormStep4
                        v-if="currentStep === 4"
                        :collaborator-form="form"
                        :initial-photo-url="collaborator_data?.photo_full_url"
                        :initial-curriculum-url="collaborator_data?.curriculum_full_url"
                    />

                    <CollaboratorFormActions
                        :current-step="currentStep"
                        :total-steps="totalSteps"
                        :form-processing="form.processing"
                        submit-button-text="Atualizar Colaborador"
                        @next="nextStep"
                        @prev="prevStep"
                        @submit="submit"
                    />
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
