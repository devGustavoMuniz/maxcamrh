<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link, usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
  collaborator_data: Object,
  clients: Array,
});

const page = usePage();
const userRole = computed(() => page.props.auth.user?.role);

const currentStep = ref(1);
const totalSteps = 4;

const photoFileInput = ref(null);
const curriculumFileInput = ref(null);

const form = useForm({
  _method: 'PUT',
  user: {
    name: props.collaborator_data.user?.name || '',
    email: props.collaborator_data.user?.email || '',
    password: '',
    password_confirmation: '',
  },
  collaborator: {
    client_id: props.collaborator_data.client_id || null,
    photo_file: null,
    curriculum_file: null,
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

const moneyConfig = {
  decimal: ',',
  thousands: '.',
  prefix: 'R$ ',
  precision: 2,
  masked: false,
};

const photoPreview = ref(props.collaborator_data.photo_full_url);
const curriculumFileName = ref(null);
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
    if (isImage) {
      previewRef.value = URL.createObjectURL(file);
    } else {
      previewRef.value = file.name;
      existingCurriculumUrl.value = null;
    }
  }
}

function nextStep() { if (currentStep.value < totalSteps) currentStep.value++; }
function prevStep() { if (currentStep.value > 1) currentStep.value--; }

const submit = () => {
  form.transform(data => {
    const transformedData = JSON.parse(JSON.stringify(data));
    const fieldsToClean = ['cpf', 'rg', 'cnh', 'phone', 'cellphone', 'emergency_phone'];
    fieldsToClean.forEach(field => {
      if (transformedData.collaborator[field]) {
        transformedData.collaborator[field] = String(transformedData.collaborator[field]).replace(/\D/g, '');
      }
    });
    if (transformedData.address.cep) {
      transformedData.address.cep = transformedData.address.cep.replace(/\D/g, '');
    }
    return transformedData;
  }).post(route('collaborators.update', props.collaborator_data.id), {
    onSuccess: () => {
      form.reset('user.password', 'user.password_confirmation');
      form.collaborator.photo_file = null;
      form.collaborator.curriculum_file = null;
      curriculumFileName.value = null;
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
      document.getElementById('address_number_edit')?.focus();
    } catch (error) {
      console.error("Erro ao buscar CEP:", error);
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
        <div class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 md:p-8">
          <form @submit.prevent="submit" class="space-y-6">

            <section v-if="currentStep === 1" class="space-y-4">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Etapa 1: Acesso e Dados Pessoais</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2" v-if="(userRole === 'admin' || userRole === 'franchise')">
                  <Label for="collaborator_client_id_edit">Cliente Associado</Label>
                  <Select v-model="form.collaborator.client_id">
                    <SelectTrigger id="collaborator_client_id_edit"><SelectValue placeholder="Selecione um cliente" /></SelectTrigger>
                    <SelectContent><SelectItem class="cursor-pointer" v-for="client in props.clients" :key="client.id" :value="client.id">{{ client.name }}</SelectItem></SelectContent>
                  </Select>
                  <InputError class="mt-2" :message="form.errors['collaborator.client_id']" />
                </div>
                <div>
                  <Label for="user_name_edit">Nome Completo</Label>
                  <Input id="user_name_edit" type="text" class="mt-1 block w-full" v-model="form.user.name" required />
                </div>
                <div>
                  <Label for="user_email_edit">Email de Acesso</Label>
                  <Input id="user_email_edit" type="email" class="mt-1 block w-full" v-model="form.user.email" required />
                </div>
                <div>
                  <Label for="collaborator_date_of_birth_edit">Data de Nascimento</Label>
                  <Input id="collaborator_date_of_birth_edit" type="date" class="mt-1 block w-full" v-model="form.collaborator.date_of_birth" />
                </div>
                <div>
                  <Label for="collaborator_gender_edit">Gênero</Label>
                  <Select v-model="form.collaborator.gender"><SelectTrigger id="collaborator_gender_edit"><SelectValue placeholder="Selecione..." /></SelectTrigger><SelectContent><SelectItem value="Masculino">Masculino</SelectItem><SelectItem value="Feminino">Feminino</SelectItem><SelectItem value="Outro">Outro</SelectItem><SelectItem value="Não Informado">Não Informar</SelectItem></SelectContent></Select>
                </div>
                <div>
                  <Label for="collaborator_marital_status_edit">Estado Civil</Label>
                  <Select v-model="form.collaborator.marital_status"><SelectTrigger id="collaborator_marital_status_edit"><SelectValue placeholder="Selecione..." /></SelectTrigger><SelectContent><SelectItem value="Solteiro(a)">Solteiro(a)</SelectItem><SelectItem value="Casado(a)">Casado(a)</SelectItem><SelectItem value="Divorciado(a)">Divorciado(a)</SelectItem><SelectItem value="Viúvo(a)">Viúvo(a)</SelectItem><SelectItem value="União Estável">União Estável</SelectItem></SelectContent></Select>
                </div>
                <div>
                  <Label for="collaborator_scholarity_edit">Escolaridade</Label>
                  <Input id="collaborator_scholarity_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.scholarity" />
                </div>
                <div>
                  <Label for="collaborator_father_name_edit">Nome do Pai</Label>
                  <Input id="collaborator_father_name_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.father_name" />
                </div>
                <div>
                  <Label for="collaborator_mother_name_edit">Nome da Mãe</Label>
                  <Input id="collaborator_mother_name_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.mother_name" />
                </div>
                <div>
                  <Label for="collaborator_nationality_edit">Nacionalidade</Label>
                  <Input id="collaborator_nationality_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.nationality" />
                </div>
                <div class="flex items-center space-x-2 self-end pb-1">
                  <Checkbox id="collaborator_is_special_needs_person_edit" :checked="form.collaborator.is_special_needs_person" @update:checked="form.collaborator.is_special_needs_person = $event" />
                  <Label for="collaborator_is_special_needs_person_edit">PCD?</Label>
                </div>
                <div class="md:col-span-2 dark:border-gray-700">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
              </div>
            </section>

            <section v-if="currentStep === 2" class="space-y-4">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Etapa 2: Contato e Endereço</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <Label for="collaborator_personal_email_edit">Email Pessoal</Label>
                  <Input id="collaborator_personal_email_edit" type="email" class="mt-1 block w-full" v-model="form.collaborator.personal_email" />
                </div>
                <div>
                  <Label for="collaborator_business_email_edit">Email Comercial</Label>
                  <Input id="collaborator_business_email_edit" type="email" class="mt-1 block w-full" v-model="form.collaborator.business_email" />
                </div>
                <div>
                  <Label for="collaborator_phone_edit">Telefone Fixo</Label>
                  <Input id="collaborator_phone_edit" type="tel" class="mt-1 block w-full" v-model="form.collaborator.phone" v-mask="['(##) ####-####', '(##) #####-####']" />
                </div>
                <div>
                  <Label for="collaborator_cellphone_edit">Celular</Label>
                  <Input id="collaborator_cellphone_edit" type="tel" class="mt-1 block w-full" v-model="form.collaborator.cellphone" v-mask="['(##) ####-####', '(##) #####-####']" />
                </div>
                <div class="md:col-span-2">
                  <Label for="collaborator_emergency_phone_edit">Telefone de Emergência</Label>
                  <Input id="collaborator_emergency_phone_edit" type="tel" class="mt-1 block w-full" v-model="form.collaborator.emergency_phone" v-mask="['(##) ####-####', '(##) #####-####']" />
                </div>
                <div>
                  <Label for="address_cep_edit">CEP</Label>
                  <Input id="address_cep_edit" type="text" class="mt-1 block w-full" v-model="form.address.cep" @blur="fetchAddressByCep" v-mask="'#####-###'" />
                </div>
                <div>
                  <Label for="address_street_edit">Logradouro (Rua, Av.)</Label>
                  <Input id="address_street_edit" type="text" class="mt-1 block w-full" v-model="form.address.street" />
                </div>
                <div>
                  <Label for="address_number_edit">Número</Label>
                  <Input id="address_number_edit" type="text" class="mt-1 block w-full" v-model="form.address.number" />
                </div>
                <div>
                  <Label for="address_complement_edit">Complemento</Label>
                  <Input id="address_complement_edit" type="text" class="mt-1 block w-full" v-model="form.address.complement" />
                </div>
                <div>
                  <Label for="address_neighborhood_edit">Bairro</Label>
                  <Input id="address_neighborhood_edit" type="text" class="mt-1 block w-full" v-model="form.address.neighborhood" />
                </div>
                <div>
                  <Label for="address_city_edit">Cidade</Label>
                  <Input id="address_city_edit" type="text" class="mt-1 block w-full" v-model="form.address.city" />
                </div>
                <div>
                  <Label for="address_state_edit">Estado (UF)</Label>
                  <Input id="address_state_edit" type="text" class="mt-1 block w-full" v-model="form.address.state" maxlength="2" />
                </div>
              </div>
            </section>

            <section v-if="currentStep === 3" class="space-y-4">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Etapa 3: Dados Contratuais</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <Label for="collaborator_department_edit">Departamento</Label>
                  <Input id="collaborator_department_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.department" />
                </div>
                <div>
                  <Label for="collaborator_position_edit">Cargo</Label>
                  <Input id="collaborator_position_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.position" />
                </div>
                <div>
                  <Label for="collaborator_type_of_contract_edit">Tipo de Contrato</Label>
                  <Select v-model="form.collaborator.type_of_contract"><SelectTrigger id="collaborator_type_of_contract_edit"><SelectValue placeholder="Selecione..." /></SelectTrigger><SelectContent><SelectItem value="CLT">CLT</SelectItem><SelectItem value="PJ">PJ</SelectItem><SelectItem value="Estágio">Estágio</SelectItem><SelectItem value="Temporário">Temporário</SelectItem></SelectContent></Select>
                </div>
                <div>
                  <Label for="collaborator_salary_edit">Salário (R$)</Label>
                  <Input id="collaborator_salary_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.salary" v-money3="moneyConfig" />
                </div>
                <div>
                  <Label for="collaborator_admission_date_edit">Data de Admissão</Label>
                  <Input id="collaborator_admission_date_edit" type="date" class="mt-1 block w-full" v-model="form.collaborator.admission_date" />
                </div>
                <div>
                  <Label for="collaborator_contract_start_date_edit">Início Efetivo Contrato</Label>
                  <Input id="collaborator_contract_start_date_edit" type="date" class="mt-1 block w-full" v-model="form.collaborator.contract_start_date" />
                </div>
                <div>
                  <Label for="collaborator_contract_expiration_edit">Fim do Contrato (opcional)</Label>
                  <Input id="collaborator_contract_expiration_edit" type="date" class="mt-1 block w-full" v-model="form.collaborator.contract_expiration" />
                </div>
                <div>
                  <Label for="collaborator_direct_superior_name_edit">Superior Direto</Label>
                  <Input id="collaborator_direct_superior_name_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.direct_superior_name" />
                </div>
                <div class="md:col-span-2">
                  <Label for="collaborator_hierarchical_degree_edit">Grau Hierárquico</Label>
                  <Input id="collaborator_hierarchical_degree_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.hierarchical_degree" />
                </div>
                <div class="md:col-span-2">
                  <Label for="collaborator_observations_edit">Observações Contratuais</Label>
                  <Textarea id="collaborator_observations_edit" class="mt-1 block w-full" v-model="form.collaborator.observations" rows="3" />
                </div>
              </div>
            </section>

            <section v-if="currentStep === 4" class="space-y-4">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Etapa 4: Documentos e Dados Bancários</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <Label for="collaborator_cpf_edit">CPF</Label>
                  <Input id="collaborator_cpf_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.cpf" v-mask="'###.###.###-##'" />
                </div>
                <div>
                  <Label for="collaborator_rg_edit">RG</Label>
                  <Input id="collaborator_rg_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.rg" v-mask="'##.###.###-A'" />
                </div>
                <div>
                  <Label for="collaborator_cnh_edit">CNH (Número)</Label>
                  <Input id="collaborator_cnh_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.cnh" v-mask="'###########'" />
                </div>
                <div>
                  <Label for="collaborator_reservista_edit">Certificado de Reservista</Label>
                  <Input id="collaborator_reservista_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.reservista" />
                </div>
                <div>
                  <Label for="collaborator_titulo_eleitor_edit">Título de Eleitor</Label>
                  <Input id="collaborator_titulo_eleitor_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.titulo_eleitor" />
                </div>
                <div>
                  <Label for="collaborator_zona_eleitoral_edit">Zona Eleitoral</Label>
                  <Input id="collaborator_zona_eleitoral_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.zona_eleitoral" />
                </div>
                <div>
                  <Label for="collaborator_pis_ctps_numero_edit">PIS/PASEP ou Nº CTPS</Label>
                  <Input id="collaborator_pis_ctps_numero_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.pis_ctps_numero" />
                </div>
                <div>
                  <Label for="collaborator_ctps_serie_edit">Série CTPS</Label>
                  <Input id="collaborator_ctps_serie_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.ctps_serie" />
                </div>
                <div class="md:col-span-2">
                  <Label>Arquivos (opcional: envie novos para substituir)</Label>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <input id="collaborator_photo_file_edit" ref="photoFileInput" type="file" class="hidden" @change="event => handleFileUpload(event, 'photo_file', photoPreview, true)" accept="image/*" />
                      <Button type="button" variant="outline" @click="photoFileInput?.click()" class="w-full bg-gray-100 justify-center">Nova Foto</Button>
                    </div>
                    <div>
                      <input id="collaborator_curriculum_file_edit" ref="curriculumFileInput" type="file" class="hidden" @change="event => handleFileUpload(event, 'curriculum_file', curriculumFileName, false)" accept=".pdf,.doc,.docx" />
                      <Button type="button" variant="outline" @click="curriculumFileInput?.click()" class="w-full bg-gray-100 justify-center">Novo Currículo</Button>
                      <p v-if="curriculumFileName" class="mt-2 text-sm text-gray-600 dark:text-gray-400 truncate text-center">{{ curriculumFileName }}</p>
                    </div>
                  </div>
                </div>
              </div>
              <h4 class="text-md font-medium text-gray-900 dark:text-gray-100">Dados Bancários</h4>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <Label for="collaborator_banco_edit">Banco</Label>
                  <Input id="collaborator_banco_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.banco" />
                </div>
                <div>
                  <Label for="collaborator_agencia_edit">Agência</Label>
                  <Input id="collaborator_agencia_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.agencia" />
                </div>
                <div>
                  <Label for="collaborator_conta_corrente_edit">Conta (com dígito)</Label>
                  <Input id="collaborator_conta_corrente_edit" type="text" class="mt-1 block w-full" v-model="form.collaborator.conta_corrente" />
                </div>
              </div>
            </section>

            <div class="flex justify-between items-center mt-8 pt-6 border-t dark:border-gray-700">
              <div>
                <Button type="button" variant="outline" class="bg-gray-100" @click="prevStep" v-if="currentStep > 1">Anterior</Button>
              </div>
              <div class="flex items-center space-x-3">
                <Link :href="route('collaborators.index')"><Button variant="outline" class="bg-gray-100" type="button">Cancelar</Button></Link>
                <Button type="button" class="bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600" @click="nextStep" v-if="currentStep < totalSteps">Próximo</Button>
                <Button type="submit" variant="black" class="bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600" :disabled="form.processing" v-if="currentStep === totalSteps">Atualizar Colaborador</Button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
