<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue'; // Adicionado watch
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
  collaborator_data: Object,
});

const currentStep = ref(1);
const totalSteps = 4;

const form = useForm({
  _method: 'PUT', // Para o update
  user: {
    name: props.collaborator_data.user?.name || '',
    email: props.collaborator_data.user?.email || '',
    password: '', // Senha é opcional na edição
    password_confirmation: '',
  },
  collaborator: {
    photo_file: null, // Para novo upload
    curriculum_file: null, // Para novo upload
    date_of_birth: props.collaborator_data.date_of_birth || '',
    gender: props.collaborator_data.gender || '',
    is_special_needs_person: props.collaborator_data.is_special_needs_person || false,
    marital_status: props.collaborator_data.marital_status || '',
    scholarity: props.collaborator_data.scholarity || '',
    father_name: props.collaborator_data.father_name || '',
    mother_name: props.collaborator_data.mother_name || '',
    nationality: props.collaborator_data.nationality || '',
    personal_email: props.collaborator_data.personal_email || '',
    business_email: props.collaborator_data.business_email || '',
    phone: props.collaborator_data.phone || '',
    cellphone: props.collaborator_data.cellphone || '',
    emergency_phone: props.collaborator_data.emergency_phone || '',
    department: props.collaborator_data.department || '',
    position: props.collaborator_data.position || '',
    type_of_contract: props.collaborator_data.type_of_contract || '',
    salary: props.collaborator_data.salary || null,
    admission_date: props.collaborator_data.admission_date || '',
    direct_superior_name: props.collaborator_data.direct_superior_name || '',
    hierarchical_degree: props.collaborator_data.hierarchical_degree || '',
    observations: props.collaborator_data.observations || '',
    contract_start_date: props.collaborator_data.contract_start_date || '',
    contract_expiration: props.collaborator_data.contract_expiration || '',
    cpf: props.collaborator_data.cpf || '',
    rg: props.collaborator_data.rg || '',
    cnh: props.collaborator_data.cnh || '',
    reservista: props.collaborator_data.reservista || '',
    titulo_eleitor: props.collaborator_data.titulo_eleitor || '',
    zona_eleitoral: props.collaborator_data.zona_eleitoral || '',
    pis_ctps_numero: props.collaborator_data.pis_ctps_numero || '',
    ctps_serie: props.collaborator_data.ctps_serie || '',
    banco: props.collaborator_data.banco || '',
    agencia: props.collaborator_data.agencia || '',
    conta_corrente: props.collaborator_data.conta_corrente || '',
  },
  address: {
    cep: props.collaborator_data.address?.cep || '',
    street: props.collaborator_data.address?.street || '',
    number: props.collaborator_data.address?.number || '',
    complement: props.collaborator_data.address?.complement || '',
    neighborhood: props.collaborator_data.address?.neighborhood || '',
    state: props.collaborator_data.address?.state || '',
    city: props.collaborator_data.address?.city || '',
  },
});

const photoPreview = ref(props.collaborator_data.photo_full_url);
const curriculumPreview = ref(null); // Nome do arquivo para novo upload
const existingCurriculumUrl = ref(props.collaborator_data.curriculum_full_url);


watch(() => props.collaborator_data.photo_full_url, (newUrl) => {
  if (!form.collaborator.photo_file) photoPreview.value = newUrl;
});
watch(() => props.collaborator_data.curriculum_full_url, (newUrl) => {
  if (!form.collaborator.curriculum_file) existingCurriculumUrl.value = newUrl;
});


function handleFileUpload(event, field, previewRef, isImage = false) {
  const file = event.target.files[0];
  if (file) {
    form.collaborator[field] = file;
    if (isImage && previewRef) {
      previewRef.value = URL.createObjectURL(file);
      if (field === 'photo_file') {
        // No edit, se uma nova foto é selecionada, não precisamos mais do link do currículo existente
      }
    } else if (previewRef) {
      previewRef.value = file.name; // Mostra nome do arquivo
      if (field === 'curriculum_file') existingCurriculumUrl.value = null;
    }
  } else {
    form.collaborator[field] = null;
    if (isImage && previewRef) previewRef.value = (field === 'photo_file' ? props.collaborator_data.photo_full_url : null);
    else if (previewRef) previewRef.value = null;

    if (field === 'curriculum_file') existingCurriculumUrl.value = props.collaborator_data.curriculum_full_url;

  }
}


function nextStep() { if (currentStep.value < totalSteps) currentStep.value++; }
function prevStep() { if (currentStep.value > 1) currentStep.value--; }

const submit = () => {
  form.post(route('collaborators.update', props.collaborator_data.id), { // form.post com _method: 'PUT'
    onSuccess: () => {
      form.reset('user.password', 'user.password_confirmation');
      form.collaborator.photo_file = null;
      form.collaborator.curriculum_file = null;
      // O watch deve cuidar de atualizar os previews com base nas props atualizadas
    },
  });
};

async function fetchAddressByCep() {
  if (form.address.cep && form.address.cep.replace(/\D/g, '').length === 8) {
    try {
      const response = await fetch(`https://viacep.com.br/ws/${form.address.cep.replace(/\D/g, '')}/json/`);
      if (!response.ok) throw new Error('CEP não encontrado');
      const data = await response.json();
      if (data.erro) throw new Error('CEP inválido');
      form.address.street = data.logradouro;
      form.address.neighborhood = data.bairro;
      form.address.city = data.localidade;
      form.address.state = data.uf;
      document.getElementById('address_number')?.focus();
    } catch (error) {
      console.error("Erro ao buscar CEP:", error);
      form.address.street = '';
      form.address.neighborhood = '';
      form.address.city = '';
      form.address.state = '';
    }
  }
}

</script>

<template>
  <Head :title="'Editar Colaborador: ' + collaborator_data.user?.name" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Editar Colaborador: {{ collaborator_data.user?.name }} (Etapa {{ currentStep }} de {{ totalSteps }})
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 md:p-8">
          <form @submit.prevent="submit" class="space-y-6">
            <section v-if="currentStep === 1" class="space-y-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Etapa 1: Acesso e Dados Pessoais</h3>
              <div>
                <Label for="user_name_edit">Nome Completo do Usuário</Label>
                <Input id="user_name_edit" type="text" class="mt-1 block w-full" v-model="form.user.name" required />
                <InputError class="mt-2" :message="form.errors['user.name']" />
              </div>
              <div>
                <Label for="user_email_edit">Email de Acesso</Label>
                <Input id="user_email_edit" type="email" class="mt-1 block w-full" v-model="form.user.email" required />
                <InputError class="mt-2" :message="form.errors['user.email']" />
              </div>
              <div class="border-t dark:border-gray-700 pt-4">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Alterar Senha (deixe em branco para não modificar)</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <Label for="user_password_edit">Nova Senha</Label>
                    <Input id="user_password_edit" type="password" class="mt-1 block w-full" v-model="form.user.password" />
                    <InputError class="mt-2" :message="form.errors['user.password']" />
                  </div>
                  <div>
                    <Label for="user_password_confirmation_edit">Confirmar Nova Senha</Label>
                    <Input id="user_password_confirmation_edit" type="password" class="mt-1 block w-full" v-model="form.user.password_confirmation" />
                  </div>
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <Label for="collaborator_photo_file_edit">Nova Foto (3x4)</Label>
                  <Input id="collaborator_photo_file_edit" type="file" @input="event => handleFileUpload(event, 'photo_file', photoPreview, true)" class="mt-1 block w-full file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" accept="image/*" />
                  <InputError class="mt-2" :message="form.errors['collaborator.photo_file']" />
                  <img v-if="photoPreview" :src="photoPreview" alt="Preview Foto" class="mt-2 h-24 w-auto rounded"/>
                  <p v-else-if="!form.collaborator.photo_file && !props.collaborator_data.photo_full_url" class="mt-2 text-sm text-gray-500">Nenhuma foto.</p>
                </div>
                <div>
                  <Label for="collaborator_curriculum_file_edit">Novo Currículo (PDF, DOC)</Label>
                  <Input id="collaborator_curriculum_file_edit" type="file" @input="event => handleFileUpload(event, 'curriculum_file', curriculumPreview, false)" class="mt-1 block w-full file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" accept=".pdf,.doc,.docx" />
                  <InputError class="mt-2" :message="form.errors['collaborator.curriculum_file']" />
                  <p v-if="curriculumPreview" class="mt-2 text-sm text-gray-500 dark:text-gray-400">Novo arquivo: {{ curriculumPreview }}</p>
                  <div v-if="existingCurriculumUrl && !curriculumPreview" class="mt-2">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Currículo Atual:</p>
                    <a :href="existingCurriculumUrl" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline break-all">{{ existingCurriculumUrl.split('/').pop() }}</a>
                  </div>
                  <p v-else-if="!existingCurriculumUrl && !curriculumPreview" class="mt-2 text-sm text-gray-500">Nenhum currículo.</p>
                </div>
              </div>
              <div>
                <Label for="collaborator_date_of_birth_edit">Data de Nascimento</Label>
                <Input id="collaborator_date_of_birth_edit" type="date" class="mt-1 block w-full" v-model="form.collaborator.date_of_birth" />
                <InputError class="mt-2" :message="form.errors['collaborator.date_of_birth']" />
              </div>
              <div>
                <Label for="collaborator_gender_edit">Gênero</Label>
                <Input id="collaborator_gender_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.gender" />
                <InputError class="mt-2" :message="form.errors['collaborator.gender']" />
              </div>
              <div class="flex items-center space-x-2 mt-1">
                <Checkbox id="collaborator_is_special_needs_person_edit" v-model:checked="form.collaborator.is_special_needs_person" />
                <Label for="collaborator_is_special_needs_person_edit">Pessoa com Necessidades Especiais?</Label>
                <InputError class="mt-2" :message="form.errors['collaborator.is_special_needs_person']" />
              </div>
              <div>
                <Label for="collaborator_marital_status_edit">Estado Civil</Label>
                <Input id="collaborator_marital_status_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.marital_status" />
                <InputError class="mt-2" :message="form.errors['collaborator.marital_status']" />
              </div>
              <div>
                <Label for="collaborator_scholarity_edit">Escolaridade</Label>
                <Input id="collaborator_scholarity_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.scholarity" />
                <InputError class="mt-2" :message="form.errors['collaborator.scholarity']" />
              </div>
              <div>
                <Label for="collaborator_father_name_edit">Nome do Pai</Label>
                <Input id="collaborator_father_name_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.father_name" />
                <InputError class="mt-2" :message="form.errors['collaborator.father_name']" />
              </div>
              <div>
                <Label for="collaborator_mother_name_edit">Nome da Mãe</Label>
                <Input id="collaborator_mother_name_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.mother_name" />
                <InputError class="mt-2" :message="form.errors['collaborator.mother_name']" />
              </div>
              <div>
                <Label for="collaborator_nationality_edit">Nacionalidade</Label>
                <Input id="collaborator_nationality_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.nationality" />
                <InputError class="mt-2" :message="form.errors['collaborator.nationality']" />
              </div>
            </section>

            <section v-if="currentStep === 2" class="space-y-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Etapa 2: Contato e Endereço</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <Label for="collaborator_personal_email_edit">Email Pessoal</Label>
                  <Input id="collaborator_personal_email_edit" type="email" class="mt-1 block w-full" v-model="form.collaborator.personal_email" />
                  <InputError class="mt-2" :message="form.errors['collaborator.personal_email']" />
                </div>
                <div>
                  <Label for="collaborator_business_email_edit">Email Comercial</Label>
                  <Input id="collaborator_business_email_edit" type="email" class="mt-1 block w-full" v-model="form.collaborator.business_email" />
                  <InputError class="mt-2" :message="form.errors['collaborator.business_email']" />
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                  <Label for="collaborator_phone_edit">Telefone Fixo</Label>
                  <Input id="collaborator_phone_edit" type="tel" class="mt-1 block w-full" v-model="form.collaborator.phone" />
                  <InputError class="mt-2" :message="form.errors['collaborator.phone']" />
                </div>
                <div>
                  <Label for="collaborator_cellphone_edit">Celular</Label>
                  <Input id="collaborator_cellphone_edit" type="tel" class="mt-1 block w-full" v-model="form.collaborator.cellphone" />
                  <InputError class="mt-2" :message="form.errors['collaborator.cellphone']" />
                </div>
                <div>
                  <Label for="collaborator_emergency_phone_edit">Telefone de Emergência</Label>
                  <Input id="collaborator_emergency_phone_edit" type="tel" class="mt-1 block w-full" v-model="form.collaborator.emergency_phone" />
                  <InputError class="mt-2" :message="form.errors['collaborator.emergency_phone']" />
                </div>
              </div>

              <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 pt-4">Endereço Principal</h4>
              <div>
                <Label for="address_cep_edit">CEP</Label>
                <Input id="address_cep_edit" type="text" class="mt-1 block w-full" v-model="form.address.cep" @blur="fetchAddressByCep" placeholder="00000-000" />
                <InputError class="mt-2" :message="form.errors['address.cep']" />
              </div>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                  <Label for="address_street_edit">Logradouro (Rua, Av.)</Label>
                  <Input id="address_street_edit" type="text" class="mt-1 block w-full" v-model="form.address.street" />
                  <InputError class="mt-2" :message="form.errors['address.street']" />
                </div>
                <div>
                  <Label for="address_number_edit">Número</Label>
                  <Input id="address_number_edit" type="text" class="mt-1 block w-full" v-model="form.address.number" />
                  <InputError class="mt-2" :message="form.errors['address.number']" />
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <Label for="address_complement_edit">Complemento</Label>
                  <Input id="address_complement_edit" type="text" class="mt-1 block w-full" v-model="form.address.complement" />
                  <InputError class="mt-2" :message="form.errors['address.complement']" />
                </div>
                <div>
                  <Label for="address_neighborhood_edit">Bairro</Label>
                  <Input id="address_neighborhood_edit" type="text" class="mt-1 block w-full" v-model="form.address.neighborhood" />
                  <InputError class="mt-2" :message="form.errors['address.neighborhood']" />
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <Label for="address_city_edit">Cidade</Label>
                  <Input id="address_city_edit" type="text" class="mt-1 block w-full" v-model="form.address.city" />
                  <InputError class="mt-2" :message="form.errors['address.city']" />
                </div>
                <div>
                  <Label for="address_state_edit">Estado (UF)</Label>
                  <Input id="address_state_edit" type="text" class="mt-1 block w-full" v-model="form.address.state" maxlength="2" />
                  <InputError class="mt-2" :message="form.errors['address.state']" />
                </div>
              </div>
            </section>

            <section v-if="currentStep === 3" class="space-y-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Etapa 3: Dados Contratuais</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <Label for="collaborator_department_edit">Departamento</Label>
                  <Input id="collaborator_department_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.department" />
                  <InputError class="mt-2" :message="form.errors['collaborator.department']" />
                </div>
                <div>
                  <Label for="collaborator_position_edit">Cargo</Label>
                  <Input id="collaborator_position_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.position" />
                  <InputError class="mt-2" :message="form.errors['collaborator.position']" />
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <Label for="collaborator_type_of_contract_edit">Tipo de Contrato</Label>
                  <Input id="collaborator_type_of_contract_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.type_of_contract" />
                  <InputError class="mt-2" :message="form.errors['collaborator.type_of_contract']" />
                </div>
                <div>
                  <Label for="collaborator_salary_edit">Salário (R$)</Label>
                  <Input id="collaborator_salary_edit" type="number" step="0.01" class="mt-1 block w-full" v-model.number="form.collaborator.salary" />
                  <InputError class="mt-2" :message="form.errors['collaborator.salary']" />
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <Label for="collaborator_admission_date_edit">Data de Admissão</Label>
                  <Input id="collaborator_admission_date_edit" type="date" class="mt-1 block w-full" v-model="form.collaborator.admission_date" />
                  <InputError class="mt-2" :message="form.errors['collaborator.admission_date']" />
                </div>
                <div>
                  <Label for="collaborator_contract_start_date_edit">Data Início Efetivo Contrato</Label>
                  <Input id="collaborator_contract_start_date_edit" type="date" class="mt-1 block w-full" v-model="form.collaborator.contract_start_date" />
                  <InputError class="mt-2" :message="form.errors['collaborator.contract_start_date']" />
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <Label for="collaborator_contract_expiration_edit">Data Fim do Contrato (se houver)</Label>
                  <Input id="collaborator_contract_expiration_edit" type="date" class="mt-1 block w-full" v-model="form.collaborator.contract_expiration" />
                  <InputError class="mt-2" :message="form.errors['collaborator.contract_expiration']" />
                </div>
                <div>
                  <Label for="collaborator_direct_superior_name_edit">Nome do Superior Direto</Label>
                  <Input id="collaborator_direct_superior_name_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.direct_superior_name" />
                  <InputError class="mt-2" :message="form.errors['collaborator.direct_superior_name']" />
                </div>
              </div>
              <div>
                <Label for="collaborator_hierarchical_degree_edit">Grau Hierárquico</Label>
                <Input id="collaborator_hierarchical_degree_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.hierarchical_degree" />
                <InputError class="mt-2" :message="form.errors['collaborator.hierarchical_degree']" />
              </div>
              <div>
                <Label for="collaborator_observations_edit">Observações Contratuais</Label>
                <Textarea id="collaborator_observations_edit" class="mt-1 block w-full" v-model="form.collaborator.observations" rows="3" />
                <InputError class="mt-2" :message="form.errors['collaborator.observations']" />
              </div>
            </section>

            <section v-if="currentStep === 4" class="space-y-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Etapa 4: Documentos e Dados Bancários</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <Label for="collaborator_cpf_edit">CPF</Label>
                  <Input id="collaborator_cpf_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.cpf" />
                  <InputError class="mt-2" :message="form.errors['collaborator.cpf']" />
                </div>
                <div>
                  <Label for="collaborator_rg_edit">RG</Label>
                  <Input id="collaborator_rg_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.rg" />
                  <InputError class="mt-2" :message="form.errors['collaborator.rg']" />
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <Label for="collaborator_cnh_edit">CNH (Número)</Label>
                  <Input id="collaborator_cnh_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.cnh" />
                  <InputError class="mt-2" :message="form.errors['collaborator.cnh']" />
                </div>
                <div>
                  <Label for="collaborator_reservista_edit">Certificado de Reservista</Label>
                  <Input id="collaborator_reservista_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.reservista" />
                  <InputError class="mt-2" :message="form.errors['collaborator.reservista']" />
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <Label for="collaborator_titulo_eleitor_edit">Título de Eleitor</Label>
                  <Input id="collaborator_titulo_eleitor_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.titulo_eleitor" />
                  <InputError class="mt-2" :message="form.errors['collaborator.titulo_eleitor']" />
                </div>
                <div>
                  <Label for="collaborator_zona_eleitoral_edit">Zona Eleitoral</Label>
                  <Input id="collaborator_zona_eleitoral_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.zona_eleitoral" />
                  <InputError class="mt-2" :message="form.errors['collaborator.zona_eleitoral']" />
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <Label for="collaborator_pis_ctps_numero_edit">PIS/PASEP ou Nº CTPS</Label>
                  <Input id="collaborator_pis_ctps_numero_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.pis_ctps_numero" />
                  <InputError class="mt-2" :message="form.errors['collaborator.pis_ctps_numero']" />
                </div>
                <div>
                  <Label for="collaborator_ctps_serie_edit">Série CTPS</Label>
                  <Input id="collaborator_ctps_serie_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.ctps_serie" />
                  <InputError class="mt-2" :message="form.errors['collaborator.ctps_serie']" />
                </div>
              </div>
              <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 pt-4">Dados Bancários</h4>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                  <Label for="collaborator_banco_edit">Banco</Label>
                  <Input id="collaborator_banco_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.banco" />
                  <InputError class="mt-2" :message="form.errors['collaborator.banco']" />
                </div>
                <div>
                  <Label for="collaborator_agencia_edit">Agência</Label>
                  <Input id="collaborator_agencia_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.agencia" />
                  <InputError class="mt-2" :message="form.errors['collaborator.agencia']" />
                </div>
                <div>
                  <Label for="collaborator_conta_corrente_edit">Conta Corrente (com dígito)</Label>
                  <Input id="collaborator_conta_corrente_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.conta_corrente" />
                  <InputError class="mt-2" :message="form.errors['collaborator.conta_corrente']" />
                </div>
              </div>
            </section>

            <div class="flex justify-between items-center mt-8 pt-6 border-t dark:border-gray-700">
              <div>
                <Button type="button" variant="outline" @click="prevStep" v-if="currentStep > 1">
                  Anterior
                </Button>
              </div>
              <div class="flex items-center space-x-3">
                <Link :href="route('collaborators.index')">
                  <Button variant="ghost" type="button">Cancelar</Button>
                </Link>
                <Button type="button" variant="default" @click="nextStep" v-if="currentStep < totalSteps">
                  Próximo
                </Button>
                <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" v-if="currentStep === totalSteps">
                  Atualizar Colaborador
                </Button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
