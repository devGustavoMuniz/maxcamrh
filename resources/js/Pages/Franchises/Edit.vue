<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/Components/InputError.vue';
import { ref, watch } from 'vue';

const props = defineProps({
  franchise_data: Object,
});

const documentFileInput = ref(null);

const form = useForm({
  _method: 'PUT',
  name: props.franchise_data.user.name,
  email: props.franchise_data.user.email,
  password: '',
  password_confirmation: '',
  maxcam_email: props.franchise_data.maxcam_email,
  cnpj: props.franchise_data.cnpj,
  max_client: props.franchise_data.max_client,
  contract_start_date: props.franchise_data.contract_start_date_form,
  actuation_region: props.franchise_data.actuation_region,
  document_file: null,
  observations: props.franchise_data.observations,
});

const documentPreview = ref(null);
const existingDocumentUrl = ref(props.franchise_data.document_full_url);

watch(() => props.franchise_data.document_full_url, (newUrl) => {
  if (!form.document_file) {
    existingDocumentUrl.value = newUrl;
    documentPreview.value = null;
  }
});

function handleDocumentUpload(event) {
  const file = event.target.files[0];
  if (file) {
    form.document_file = file;
    documentPreview.value = file.name;
    existingDocumentUrl.value = null;
  } else {
    form.document_file = null;
    documentPreview.value = null;
    existingDocumentUrl.value = props.franchise_data.document_full_url;
  }
}

const submit = () => {
  // Limpa a máscara do CNPJ antes de enviar
  form.transform(data => ({
    ...data,
    cnpj: data.cnpj.replace(/\D/g, ''),
  })).post(route('franchises.update', props.franchise_data.id), {
    onSuccess: () => {
      form.reset('password', 'password_confirmation');
      form.document_file = null;
    },
  });
};
</script>

<template>
  <Head :title="'Editar Franqueado: ' + franchise_data.user.name"/>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Editar Franqueado: {{ franchise_data.user.name }}
      </h2>
    </template>

    <div class="mx-auto w-full">
        <div class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 md:p-8">
          <form @submit.prevent="submit" class="space-y-6">

            <div class="space-y-4">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b dark:border-gray-700 pb-2">Dados
                de Acesso</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <Label for="user_name">Nome do Usuário</Label>
                  <Input id="user_name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus/>
                  <InputError class="mt-2" :message="form.errors.name"/>
                </div>
                <div>
                  <Label for="user_email">Email do Usuário</Label>
                  <Input id="user_email" type="email" class="mt-1 block w-full" v-model="form.email" required/>
                  <InputError class="mt-2" :message="form.errors.email"/>
                </div>

                <div>
                  <Label for="franchise_maxcam_email">Email MaxCam (Franquia)</Label>
                  <Input id="franchise_maxcam_email" type="email" class="mt-1 block w-full" v-model="form.maxcam_email"
                         required/>
                  <InputError class="mt-2" :message="form.errors.maxcam_email"/>
                </div>
                <div>
                  <Label for="franchise_cnpj">CNPJ</Label>
                  <Input id="franchise_cnpj" type="text" class="mt-1 block w-full" v-model="form.cnpj"
                         v-mask="'##.###.###/####-##'" required/>
                  <InputError class="mt-2" :message="form.errors.cnpj"/>
                </div>
                <div>
                  <Label for="franchise_max_client">Máximo de Clientes</Label>
                  <Input id="franchise_max_client" type="number" class="mt-1 block w-full"
                         v-model.number="form.max_client" required min="0"/>
                  <InputError class="mt-2" :message="form.errors.max_client"/>
                </div>
                <div>
                  <Label for="franchise_contract_start_date">Data Início do Contrato</Label>
                  <Input id="franchise_contract_start_date" type="date" class="mt-1 block w-full"
                         v-model="form.contract_start_date" required/>
                  <InputError class="mt-2" :message="form.errors.contract_start_date"/>
                </div>
                <div>
                  <Label for="franchise_actuation_region">Região de Atuação</Label>
                  <Input id="franchise_actuation_region" type="text" class="mt-1 block w-full"
                         v-model="form.actuation_region" required/>
                  <InputError class="mt-2" :message="form.errors.actuation_region"/>
                </div>
                <div>
                  <Label>Novo Documento (opcional)</Label>
                  <div class="mt-1 flex items-center gap-4">
                    <input ref="documentFileInput" type="file" class="hidden" @change="handleDocumentUpload"/>
                    <Button type="button" variant="outline" @click="documentFileInput?.click()">Escolher Arquivo
                    </Button>
                  </div>
                  <InputError class="mt-2" :message="form.errors.document_file"/>
                  <div v-if="documentPreview" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Novo: {{ documentPreview }}
                  </div>
                  <div v-if="existingDocumentUrl && !documentPreview" class="mt-2">
                    <a :href="existingDocumentUrl" target="_blank"
                       class="text-sm text-blue-600 dark:text-blue-400 hover:underline break-all">Ver documento
                      atual</a>
                  </div>
                </div>
                <div class="md:col-span-2">
                  <Label for="franchise_observations">Observações</Label>
                  <Textarea id="franchise_observations" class="mt-1 block w-full" v-model="form.observations" rows="3"/>
                  <InputError class="mt-2" :message="form.errors.observations"/>
                </div>
                <div>
                  <Label for="user_password">Nova Senha</Label>
                  <Input id="user_password" type="password" class="mt-1 block w-full" v-model="form.password"/>
                  <InputError class="mt-2" :message="form.errors.password"/>
                </div>
                <div>
                  <Label for="user_password_confirmation">Confirmar Nova Senha</Label>
                  <Input id="user_password_confirmation" type="password" class="mt-1 block w-full"
                         v-model="form.password_confirmation"/>
                  <InputError class="mt-2" :message="form.errors.password_confirmation"/>
                </div>
              </div>
            </div>

            <div class="flex items-center justify-end mt-6 pt-6 border-t dark:border-gray-700">
              <Link :href="route('franchises.index')" class="mr-4">
                <Button variant="outline" class="bg-gray-100" type="button">Cancelar</Button>
              </Link>
              <Button type="submit" variant="black" class="bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600"
                      :disabled="form.processing">
                Atualizar Franqueado
              </Button>
            </div>
          </form>
        </div>
      </div>
  </AuthenticatedLayout>
</template>
