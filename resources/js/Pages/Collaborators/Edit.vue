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
    collaboratorData: {
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
        name: props.collaboratorData.data.user?.name || "",
        email: props.collaboratorData.data.user?.email || "",
        password: "",
        password_confirmation: "",
    },
    collaborator: {
        client_id: props.collaboratorData.data.client_id || null,
        photo_file: null,
        curriculum_file: null,
        date_of_birth: props.collaboratorData.data.date_of_birth || "",
        gender: props.collaboratorData.data.gender || "",
        is_special_needs_person:
            props.collaboratorData.data.is_special_needs_person || false,
        marital_status: props.collaboratorData.data.marital_status || "",
        scholarity: props.collaboratorData.data.scholarity || "",
        father_name: props.collaboratorData.data.father_name || "",
        mother_name: props.collaboratorData.data.mother_name || "",
        nationality: props.collaboratorData.data.nationality || "",
        personal_email: props.collaboratorData.data.personal_email || "",
        business_email: props.collaboratorData.data.business_email || "",
        phone: props.collaboratorData.data.phone || "",
        cellphone: props.collaboratorData.data.cellphone || "",
        emergency_phone: props.collaboratorData.data.emergency_phone || "",
        department: props.collaboratorData.data.department || "",
        position: props.collaboratorData.data.position || "",
        type_of_contract: props.collaboratorData.data.type_of_contract || "",
        salary: props.collaboratorData.data.salary || null,
        admission_date: props.collaboratorData.data.admission_date || "",
        direct_superior_name:
            props.collaboratorData.data.direct_superior_name || "",
        hierarchical_degree: props.collaboratorData.data.hierarchical_degree || "",
        observations: props.collaboratorData.data.observations || "",
        contract_start_date: props.collaboratorData.data.contract_start_date || "",
        contract_expiration: props.collaboratorData.data.contract_expiration || "",
        cpf: props.collaboratorData.data.cpf || "",
        rg: props.collaboratorData.data.rg || "",
        cnh: props.collaboratorData.data.cnh || "",
        reservista: props.collaboratorData.data.reservista || "",
        titulo_eleitor: props.collaboratorData.data.titulo_eleitor || "",
        zona_eleitoral: props.collaboratorData.data.zona_eleitoral || "",
        pis_ctps_numero: props.collaboratorData.data.pis_ctps_numero || "",
        ctps_serie: props.collaboratorData.data.ctps_serie || "",
        banco: props.collaboratorData.data.banco || "",
        agencia: props.collaboratorData.data.agencia || "",
        conta_corrente: props.collaboratorData.data.conta_corrente || "",
    },
    address: {
        cep: props.collaboratorData.data.address?.cep || "",
        street: props.collaboratorData.data.address?.street || "",
        number: props.collaboratorData.data.address?.number || "",
        complement: props.collaboratorData.data.address?.complement || "",
        neighborhood: props.collaboratorData.data.address?.neighborhood || "",
        state: props.collaboratorData.data.address?.state || "",
        city: props.collaboratorData.data.address?.city || "",
    },
});

const submit = () => {
    form.post(route("collaborators.update", props.collaboratorData.data.id), {
        onSuccess: () => {
            form.reset("user.password", "user.password_confirmation");
            form.collaborator.photo_file = null;
            form.collaborator.curriculum_file = null;
        },
    });
};
</script>

<template>
    <Head :title="'Editar Colaborador: ' + collaboratorData.data.user?.name" />
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Editar Colaborador: {{ collaboratorData.data.user?.name }} (Etapa
                {{ currentStep }})
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
                    <CollaboratorFormStep4
                        v-if="currentStep === 4"
                        :collaborator-form="form"
                        :initial-photo-url="collaboratorData.data.photo_full_url"
                        :initial-curriculum-url="collaboratorData.data.curriculum_full_url"
                    />

                    <CollaboratorFormNavigation
                        :current-step="currentStep"
                        :total-steps="totalSteps"
                        :step-names="stepNames"
                        :form-processing="form.processing"
                        submit-button-text="Atualizar Colaborador"
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
