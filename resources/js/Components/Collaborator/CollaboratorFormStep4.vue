<script setup>
import { ref, watch } from "vue";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import { Button } from "@/Components/ui/button";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    collaboratorForm: {
        type: Object,
        required: true,
    },
    initialPhotoUrl: {
        type: String,
        default: null,
    },
    initialCurriculumUrl: {
        type: String,
        default: null,
    },
});

const form = props.collaboratorForm;

const photoFileInput = ref(null);
const curriculumFileInput = ref(null);

const photoPreview = ref(props.initialPhotoUrl);
const curriculumFileName = ref(props.initialCurriculumUrl ? props.initialCurriculumUrl.split('/').pop() : null);

watch(
    () => props.initialPhotoUrl,
    (newUrl) => {
        if (!form.collaborator.photo_file) photoPreview.value = newUrl;
    },
);
watch(
    () => props.initialCurriculumUrl,
    (newUrl) => {
        if (!form.collaborator.curriculum_file)
            curriculumFileName.value = newUrl ? newUrl.split('/').pop() : null;
    },
);

function handleFileUpload(event, field, previewRef, isPhoto) {
    const file = event.target.files[0];
    if (file) {
        form.collaborator[field] = file;
        if (isPhoto) {
            previewRef.value = URL.createObjectURL(file);
        } else {
            previewRef.value = file.name;
            curriculumFileName.value = null;
        }
    } else {
        form.collaborator[field] = null;
        previewRef.value = null;
    }
}
</script>

<template>
    <section class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <Label for="collaborator_cpf">CPF</Label>
                <Input
                    id="collaborator_cpf"
                    v-model="form.collaborator.cpf"
                    v-mask="'###.###.###-##'"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.cpf']"
                />
            </div>

            <div>
                <Label for="collaborator_rg">RG</Label>
                <Input
                    id="collaborator_rg"
                    v-model="form.collaborator.rg"
                    v-mask="'##.###-###'"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.rg']"
                />
            </div>

            <div>
                <Label for="collaborator_cnh">CNH (Número)</Label>
                <Input
                    id="collaborator_cnh"
                    v-model="form.collaborator.cnh"
                    v-mask="'###########'"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.cnh']"
                />
            </div>

            <div>
                <Label for="collaborator_reservista"
                    >Certificado de Reservista</Label
                >
                <Input
                    id="collaborator_reservista"
                    v-model="form.collaborator.reservista"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.reservista']"
                />
            </div>
            <div>
                <Label for="collaborator_titulo_eleitor"
                    >Título de Eleitor</Label
                >
                <Input
                    id="collaborator_titulo_eleitor"
                    v-model="form.collaborator.titulo_eleitor"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.titulo_eleitor']"
                />
            </div>
            <div>
                <Label for="collaborator_zona_eleitoral">Zona Eleitoral</Label>
                <Input
                    id="collaborator_zona_eleitoral"
                    v-model="form.collaborator.zona_eleitoral"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.zona_eleitoral']"
                />
            </div>
            <div>
                <Label for="collaborator_pis_ctps_numero"
                    >PIS/PASEP ou Nº CTPS</Label
                >
                <Input
                    id="collaborator_pis_ctps_numero"
                    v-model="form.collaborator.pis_ctps_numero"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.pis_ctps_numero']"
                />
            </div>
            <div>
                <Label for="collaborator_ctps_serie">Série CTPS</Label>
                <Input
                    id="collaborator_ctps_serie"
                    v-model="form.collaborator.ctps_serie"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.ctps_serie']"
                />
            </div>
            <div class="md:col-span-2">
                <Label>Arquivos</Label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <input
                            id="collaborator_photo_file"
                            ref="photoFileInput"
                            type="file"
                            class="hidden"
                            accept="image/*"
                            @change="
                                (event) =>
                                    handleFileUpload(
                                        event,
                                        'photo_file',
                                        photoPreview,
                                        true,
                                    )
                            "
                        />
                        <Button
                            type="button"
                            variant="outline"
                            class="w-full bg-white dark:bg-gray-700 justify-center"
                            @click="photoFileInput?.click()"
                        >Foto (3x4)
                        </Button>
                        <img
                            v-if="photoPreview"
                            :src="photoPreview"
                            alt="Preview Foto"
                            class="mt-2 h-24 rounded mx-auto"
                        />
                    </div>
                    <div>
                        <input
                            id="collaborator_curriculum_file"
                            ref="curriculumFileInput"
                            type="file"
                            class="hidden"
                            accept=".pdf,.doc,.docx"
                            @change="
                                (event) =>
                                    handleFileUpload(
                                        event,
                                        'curriculum_file',
                                        curriculumFileName,
                                        false,
                                    )
                            "
                        />
                        <Button
                            type="button"
                            variant="outline"
                            class="w-full bg-white dark:bg-gray-700 justify-center"
                            @click="curriculumFileInput?.click()"
                        >Currículo
                        </Button>
                        <p
                            v-if="curriculumFileName"
                            class="mt-2 text-sm text-gray-600 dark:text-gray-400 truncate text-center"
                        >
                            {{ curriculumFileName }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <h4
            class="text-md font-medium text-gray-900 dark:text-gray-100 pt-4"
        >
            Dados Bancários
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <Label for="collaborator_banco">Banco</Label>
                <Input
                    id="collaborator_banco"
                    v-model="form.collaborator.banco"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.banco']"
                />
            </div>
            <div>
                <Label for="collaborator_agencia">Agência</Label>
                <Input
                    id="collaborator_agencia"
                    v-model="form.collaborator.agencia"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.agencia']"
                />
            </div>
            <div>
                <Label for="collaborator_conta_corrente"
                    >Conta (com dígito)</Label
                >
                <Input
                    id="collaborator_conta_corrente"
                    v-model="form.collaborator.conta_corrente"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.conta_corrente']"
                />
            </div>
        </div>
    </section>
</template>
