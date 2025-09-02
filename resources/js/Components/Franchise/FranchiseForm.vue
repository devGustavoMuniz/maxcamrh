<script setup>
import { ref } from "vue";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import { Textarea } from "@/Components/ui/textarea";
import InputError from "@/Components/InputError.vue";
import { Button } from "@/Components/ui/button";
import { Download } from "lucide-vue-next";
import { downloadFile } from "@/utils/fileUtils";

const props = defineProps({
    franchiseForm: {
        type: Object,
        default: () => ({}),
    },
    isEdit: { type: Boolean, default: false },
    initialDocumentUrl: { type: String, default: null },
});

const form = props.franchiseForm;

const documentFileInput = ref(null);
const documentPreview = ref(props.initialDocumentUrl);

function handleDocumentUpload(event) {
    const file = event.target.files[0];
    if (file) {
        form.document_file = file;
        documentPreview.value = file.name;
    } else {
        form.document_file = null;
        documentPreview.value = props.initialDocumentUrl;
    }
}

function downloadDocument() {
    downloadFile(props.initialDocumentUrl);
}
</script>

<template>
    <div class="space-y-4">
        <h3
            class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b dark:border-gray-700 pb-2"
        >
            Dados de Acesso do Franqueado
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <Label for="user_name">Nome do Usuário</Label>
                <Input
                    id="user_name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full bg-white"
                    required
                    autofocus
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <div>
                <Label for="user_email">Email do Usuário</Label>
                <Input
                    id="user_email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full bg-white"
                    required
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div>
                <Label for="user_password">Senha</Label>
                <Input
                    id="user_password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full bg-white"
                    :required="!isEdit"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>
            <div>
                <Label for="user_password_confirmation">Confirmar Senha</Label>
                <Input
                    id="user_password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full bg-white"
                    :required="!isEdit"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>
        </div>
    </div>

    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <Label for="franchise_maxcam_email">Email MaxCam (Franquia)</Label>
                <Input
                    id="franchise_maxcam_email"
                    v-model="form.maxcam_email"
                    type="email"
                    class="mt-1 block w-full bg-white"
                    required
                />
                <InputError class="mt-2" :message="form.errors.maxcam_email" />
            </div>
            <div>
                <Label for="franchise_cnpj">CNPJ</Label>
                <Input
                    id="franchise_cnpj"
                    v-model="form.cnpj"
                    v-mask="'##.###.###/####-##'"
                    type="text"
                    class="mt-1 block w-full bg-white"
                    required
                />
                <InputError class="mt-2" :message="form.errors.cnpj" />
            </div>
            <div>
                <Label for="franchise_max_client">Máximo de Clientes</Label>
                <Input
                    id="franchise_max_client"
                    v-model.number="form.max_client"
                    type="number"
                    class="mt-1 block w-full bg-white"
                    required
                    min="0"
                />
                <InputError class="mt-2" :message="form.errors.max_client" />
            </div>
            <div>
                <Label for="franchise_contract_start_date"
                    >Data Início do Contrato</Label
                >
                <Input
                    id="franchise_contract_start_date"
                    v-model="form.contract_start_date"
                    type="date"
                    class="mt-1 block w-full bg-white"
                    required
                />
                <InputError
                    class="mt-2"
                    :message="form.errors.contract_start_date"
                />
            </div>
            <div>
                <Label for="franchise_actuation_region"
                    >Região de Atuação</Label
                >
                <Input
                    id="franchise_actuation_region"
                    v-model="form.actuation_region"
                    type="text"
                    class="mt-1 block w-full bg-white"
                    required
                />
                <InputError
                    class="mt-2"
                    :message="form.errors.actuation_region"
                />
            </div>
            <div>
                <Label>Documento (PDF, DOC, Imagem)</Label>
                <div class="mt-1 flex items-center gap-4">
                    <input
                        ref="documentFileInput"
                        type="file"
                        class="hidden"
                        @change="handleDocumentUpload"
                    />
                    <Button
                        type="button"
                        variant="outline"
                        @click="documentFileInput?.click()"
                        >Escolher Arquivo</Button
                    >
                    <Button
                        v-if="props.initialDocumentUrl"
                        type="button"
                        variant="outline"
                        class="ml-2"
                        title="Baixar documento atual"
                        @click="downloadDocument"
                    >
                        <Download class="h-4 w-4" />
                    </Button>
                    <span
                        v-if="documentPreview && !props.initialDocumentUrl"
                        class="text-sm text-gray-500 dark:text-gray-400 truncate ml-2"
                        >{{ documentPreview }}</span
                    >
                </div>
                <InputError class="mt-2" :message="form.errors.document_file" />
            </div>
            <div class="md:col-span-2">
                <Label for="franchise_observations">Observações</Label>
                <Textarea
                    id="franchise_observations"
                    v-model="form.observations"
                    class="mt-1 block w-full bg-white"
                    rows="3"
                />
                <InputError class="mt-2" :message="form.errors.observations" />
            </div>
        </div>
    </div>
</template>
