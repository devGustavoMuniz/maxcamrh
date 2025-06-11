<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
  clients: Array,
});

const page = usePage();
const userRole = computed(() => page.props.auth.user?.role);

const currentStep = ref(1);
const totalSteps = 4;

const photoFileInput = ref(null);
const curriculumFileInput = ref(null);

const form = useForm({
  user: { name: '', email: '', password: '', password_confirmation: '' },
  collaborator: {
    client_id: null, photo_file: null, curriculum_file: null, date_of_birth: '', gender: '',
    is_special_needs_person: false, marital_status: '', scholarity: '', father_name: '', mother_name: '',
    nationality: '', personal_email: '', business_email: '', phone: '', cellphone: '', emergency_phone: '',
    department: '', position: '', type_of_contract: '', salary: null, admission_date: '', direct_superior_name: '',
    hierarchical_degree: '', observations: '', contract_start_date: '', contract_expiration: '',
    cpf: '', rg: '', cnh: '', reservista: '', titulo_eleitor: '', zona_eleitoral: '',
    pis_ctps_numero: '', ctps_serie: '', banco: '', agencia: '', conta_corrente: '',
  },
  address: { cep: '', street: '', number: '', complement: '', neighborhood: '', state: '', city: '' },
});

const moneyConfig = {
  decimal: ',',
  thousands: '.',
  prefix: 'R$ ',
  precision: 2,
  masked: false,
};

const photoPreview = ref(null);
const curriculumFileName = ref(null);

function handleFileUpload(event, field, previewRef, isPhoto) {
  const file = event.target.files[0];
  if (file) {
    form.collaborator[field] = file;
    if (isPhoto) {
      previewRef.value = URL.createObjectURL(file);
    } else {
      previewRef.value = file.name;
    }
  } else {
    form.collaborator[field] = null;
    previewRef.value = null;
  }
}

function nextStep() { if (currentStep.value < totalSteps) currentStep.value++; }
function prevStep() { if (currentStep.value > 1) currentStep.value--; }

const submit = () => {
  form.post(route('collaborators.store'), {
    onSuccess: () => {
      form.reset();
      photoPreview.value = null;
      curriculumFileName.value = null;
      currentStep.value = 1;
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
    }
  }
}
</script>

<template>
  <Head title="Adicionar Colaborador" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Adicionar Novo Colaborador (Etapa {{ currentStep }} de {{ totalSteps }})
      </h2>
    </template>

    <div class="py-6">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 md:p-8">
          <form @submit.prevent="submit" class="space-y-6">

            <section v-if="currentStep === 1" class="space-y-4">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Etapa 1: Acesso e Dados Pessoais</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2" v-if="(userRole === 'admin' || userRole === 'franchise') && props.clients && props.clients.length > 0">
                  <Label for="collaborator_client_id">Cliente Associado</Label>
                  <Select v-model="form.collaborator.client_id">
                    <SelectTrigger id="collaborator_client_id"><SelectValue placeholder="Selecione um cliente" /></SelectTrigger>
                    <SelectContent><SelectItem class="cursor-pointer" v-for="client in props.clients" :key="client.id" :value="client.id">{{ client.name }}</SelectItem></SelectContent>
                  </Select>
                  <InputError class="mt-2" :message="form.errors['collaborator.client_id']" />
                </div>
                <div class="md:col-span-2" v-if="userRole === 'client'">
                  <p class="p-3 bg-blue-50 dark:bg-blue-900/50 border border-blue-200 dark:border-blue-700 rounded-md text-sm text-blue-700 dark:text-blue-300">Este colaborador será associado automaticamente ao seu perfil de cliente.</p>
                </div>
                <div>
                  <Label for="user_name">Nome Completo</Label>
                  <Input id="user_name" type="text" class="mt-1 block w-full" v-model="form.user.name" required />
                  <InputError class="mt-2" :message="form.errors['user.name']" />
                </div>
                <div>
                  <Label for="user_email">Email de Acesso</Label>
                  <Input id="user_email" type="email" class="mt-1 block w-full" v-model="form.user.email" required />
                  <InputError class="mt-2" :message="form.errors['user.email']" />
                </div>
                <div>
                  <Label for="user_password">Senha</Label>
                  <Input id="user_password" type="password" class="mt-1 block w-full" v-model="form.user.password" required />
                  <InputError class="mt-2" :message="form.errors['user.password']" />
                </div>
                <div>
                  <Label for="user_password_confirmation">Confirmar Senha</Label>
                  <Input id="user_password_confirmation" type="password" class="mt-1 block w-full" v-model="form.user.password_confirmation" required />
                </div>
                <div>
                  <Label for="collaborator_date_of_birth">Data de Nascimento</Label>
                  <Input id="collaborator_date_of_birth" type="date" class="mt-1 block w-full" v-model="form.collaborator.date_of_birth" />
                  <InputError class="mt-2" :message="form.errors['collaborator.date_of_birth']" />
                </div>
                <div>
                  <Label for="collaborator_gender">Gênero</Label>
                  <Select v-model="form.collaborator.gender"><SelectTrigger><SelectValue placeholder="Selecione..." /></SelectTrigger><SelectContent><SelectItem value="Masculino">Masculino</SelectItem><SelectItem value="Feminino">Feminino</SelectItem><SelectItem value="Outro">Outro</SelectItem><SelectItem value="Não Informado">Não Informar</SelectItem></SelectContent></Select>
                  <InputError class="mt-2" :message="form.errors['collaborator.gender']" />
                </div>
                <div>
                  <Label for="collaborator_marital_status">Estado Civil</Label>
                  <Select v-model="form.collaborator.marital_status"><SelectTrigger><SelectValue placeholder="Selecione..." /></SelectTrigger><SelectContent><SelectItem value="Solteiro(a)">Solteiro(a)</SelectItem><SelectItem value="Casado(a)">Casado(a)</SelectItem><SelectItem value="Divorciado(a)">Divorciado(a)</SelectItem><SelectItem value="Viúvo(a)">Viúvo(a)</SelectItem><SelectItem value="União Estável">União Estável</SelectItem></SelectContent></Select>
                  <InputError class="mt-2" :message="form.errors['collaborator.marital_status']" />
                </div>
                <div>
                  <Label for="collaborator_scholarity">Escolaridade</Label>
                  <Input id="collaborator_scholarity" type="text" class="mt-1 block w-full" v-model="form.collaborator.scholarity" />
                  <InputError class="mt-2" :message="form.errors['collaborator.scholarity']" />
                </div>
                <div>
                  <Label for="collaborator_father_name">Nome do Pai</Label>
                  <Input id="collaborator_father_name" type="text" class="mt-1 block w-full" v-model="form.collaborator.father_name" />
                  <InputError class="mt-2" :message="form.errors['collaborator.father_name']" />
                </div>
                <div>
                  <Label for="collaborator_mother_name">Nome da Mãe</Label>
                  <Input id="collaborator_mother_name" type="text" class="mt-1 block w-full" v-model="form.collaborator.mother_name" />
                  <InputError class="mt-2" :message="form.errors['collaborator.mother_name']" />
                </div>
                <div>
                  <Label for="collaborator_nationality">Nacionalidade</Label>
                  <Input id="collaborator_nationality" type="text" class="mt-1 block w-full" v-model="form.collaborator.nationality" />
                  <InputError class="mt-2" :message="form.errors['collaborator.nationality']" />
                </div>
                <div class="flex items-center space-x-2 self-end pb-1">
                  <Checkbox id="collaborator_is_special_needs_person" v-model:checked="form.collaborator.is_special_needs_person" />
                  <Label for="collaborator_is_special_needs_person">Pessoa com Necessidades Especiais?</Label>
                  <InputError class="mt-2" :message="form.errors['collaborator.is_special_needs_person']" />
                </div>
              </div>
            </section>
            <section v-if="currentStep === 2" class="space-y-4">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Etapa 2: Contato e Endereço</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <Label for="collaborator_personal_email">Email Pessoal</Label>
                  <Input id="collaborator_personal_email" type="email" class="mt-1 block w-full" v-model="form.collaborator.personal_email" />
                  <InputError class="mt-2" :message="form.errors['collaborator.personal_email']" />
                </div>
                <div>
                  <Label for="collaborator_business_email">Email Comercial</Label>
                  <Input id="collaborator_business_email" type="email" class="mt-1 block w-full" v-model="form.collaborator.business_email" />
                  <InputError class="mt-2" :message="form.errors['collaborator.business_email']" />
                </div>
                <div>
                  <Label for="collaborator_phone">Telefone Fixo</Label>
                  <Input id="collaborator_phone" type="tel" class="mt-1 block w-full" v-model="form.collaborator.phone" v-mask="['(##) ####-####', '(##) #####-####']" />
                  <InputError class="mt-2" :message="form.errors['collaborator.phone']" />
                </div>
                <div>
                  <Label for="collaborator_cellphone">Celular</Label>
                  <Input id="collaborator_cellphone" type="tel" class="mt-1 block w-full" v-model="form.collaborator.cellphone" v-mask="['(##) ####-####', '(##) #####-####']" />
                  <InputError class="mt-2" :message="form.errors['collaborator.cellphone']" />
                </div>
                <div class="md:col-span-2">
                  <Label for="collaborator_emergency_phone">Telefone de Emergência</Label>
                  <Input id="collaborator_emergency_phone" type="tel" class="mt-1 block w-full" v-model="form.collaborator.emergency_phone" v-mask="['(##) ####-####', '(##) #####-####']" />
                  <InputError class="mt-2" :message="form.errors['collaborator.emergency_phone']" />
                </div>
                <div>
                  <Label for="address_cep">CEP</Label>
                  <Input id="address_cep" type="text" class="mt-1 block w-full" v-model="form.address.cep" @blur="fetchAddressByCep" v-mask="'#####-###'" />
                  <InputError class="mt-2" :message="form.errors['address.cep']" />
                </div>
                <div>
                  <Label for="address_street">Logradouro (Rua, Av.)</Label>
                  <Input id="address_street" type="text" class="mt-1 block w-full" v-model="form.address.street" />
                  <InputError class="mt-2" :message="form.errors['address.street']" />
                </div>
                <div>
                  <Label for="address_number">Número</Label>
                  <Input id="address_number" type="text" class="mt-1 block w-full" v-model="form.address.number" />
                  <InputError class="mt-2" :message="form.errors['address.number']" />
                </div>
                <div>
                  <Label for="address_complement">Complemento</Label>
                  <Input id="address_complement" type="text" class="mt-1 block w-full" v-model="form.address.complement" />
                  <InputError class="mt-2" :message="form.errors['address.complement']" />
                </div>
                <div>
                  <Label for="address_neighborhood">Bairro</Label>
                  <Input id="address_neighborhood" type="text" class="mt-1 block w-full" v-model="form.address.neighborhood" />
                  <InputError class="mt-2" :message="form.errors['address.neighborhood']" />
                </div>
                <div>
                  <Label for="address_city">Cidade</Label>
                  <Input id="address_city" type="text" class="mt-1 block w-full" v-model="form.address.city" />
                  <InputError class="mt-2" :message="form.errors['address.city']" />
                </div>
                <div>
                  <Label for="address_state">Estado (UF)</Label>
                  <Input id="address_state" type="text" class="mt-1 block w-full" v-model="form.address.state" maxlength="2" />
                  <InputError class="mt-2" :message="form.errors['address.state']" />
                </div>
              </div>
            </section>
            <section v-if="currentStep === 3" class="space-y-4">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Etapa 3: Dados Contratuais</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <Label for="collaborator_department">Departamento</Label>
                  <Input id="collaborator_department" type="text" class="mt-1 block w-full" v-model="form.collaborator.department" />
                  <InputError class="mt-2" :message="form.errors['collaborator.department']" />
                </div>
                <div>
                  <Label for="collaborator_position">Cargo</Label>
                  <Input id="collaborator_position" type="text" class="mt-1 block w-full" v-model="form.collaborator.position" />
                  <InputError class="mt-2" :message="form.errors['collaborator.position']" />
                </div>
                <div>
                  <Label for="collaborator_type_of_contract">Tipo de Contrato</Label>
                  <Select v-model="form.collaborator.type_of_contract"><SelectTrigger><SelectValue placeholder="Selecione..." /></SelectTrigger><SelectContent><SelectItem value="CLT">CLT</SelectItem><SelectItem value="PJ">PJ</SelectItem><SelectItem value="Estágio">Estágio</SelectItem><SelectItem value="Temporário">Temporário</SelectItem></SelectContent></Select>
                  <InputError class="mt-2" :message="form.errors['collaborator.type_of_contract']" />
                </div>
                <div>
                  <Label for="collaborator_salary">Salário (R$)</Label>
                  <Input id="collaborator_salary" type="text" class="mt-1 block w-full" v-model="form.collaborator.salary" v-money3="moneyConfig" />
                  <InputError class="mt-2" :message="form.errors['collaborator.salary']" />
                </div>
                <div>
                  <Label for="collaborator_admission_date">Data de Admissão</Label>
                  <Input id="collaborator_admission_date" type="date" class="mt-1 block w-full" v-model="form.collaborator.admission_date" />
                  <InputError class="mt-2" :message="form.errors['collaborator.admission_date']" />
                </div>
                <div>
                  <Label for="collaborator_contract_start_date">Data Início Efetivo Contrato</Label>
                  <Input id="collaborator_contract_start_date" type="date" class="mt-1 block w-full" v-model="form.collaborator.contract_start_date" />
                  <InputError class="mt-2" :message="form.errors['collaborator.contract_start_date']" />
                </div>
                <div>
                  <Label for="collaborator_contract_expiration">Data Fim do Contrato (opcional)</Label>
                  <Input id="collaborator_contract_expiration" type="date" class="mt-1 block w-full" v-model="form.collaborator.contract_expiration" />
                  <InputError class="mt-2" :message="form.errors['collaborator.contract_expiration']" />
                </div>
                <div>
                  <Label for="collaborator_direct_superior_name">Nome do Superior Direto</Label>
                  <Input id="collaborator_direct_superior_name" type="text" class="mt-1 block w-full" v-model="form.collaborator.direct_superior_name" />
                  <InputError class="mt-2" :message="form.errors['collaborator.direct_superior_name']" />
                </div>
                <div class="md:col-span-2">
                  <Label for="collaborator_hierarchical_degree">Grau Hierárquico</Label>
                  <Input id="collaborator_hierarchical_degree" type="text" class="mt-1 block w-full" v-model="form.collaborator.hierarchical_degree" />
                  <InputError class="mt-2" :message="form.errors['collaborator.hierarchical_degree']" />
                </div>
                <div class="md:col-span-2">
                  <Label for="collaborator_observations">Observações Contratuais</Label>
                  <Textarea id="collaborator_observations" class="mt-1 block w-full" v-model="form.collaborator.observations" rows="3" />
                  <InputError class="mt-2" :message="form.errors['collaborator.observations']" />
                </div>
              </div>
            </section>

            <section v-if="currentStep === 4" class="space-y-4">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Etapa 4: Documentos e Dados Bancários</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <Label for="collaborator_cpf">CPF</Label>
                  <Input id="collaborator_cpf" type="text" class="mt-1 block w-full" v-model="form.collaborator.cpf" v-mask="'###.###.###-##'" />
                  <InputError class="mt-2" :message="form.errors['collaborator.cpf']" />
                </div>

                <div>
                  <Label for="collaborator_rg">RG</Label>
                  <Input id="collaborator_rg" type="text" class="mt-1 block w-full" v-model="form.collaborator.rg" v-mask="'##.###-###'" />
                  <InputError class="mt-2" :message="form.errors['collaborator.rg']" />
                </div>

                <div>
                  <Label for="collaborator_cnh">CNH (Número)</Label>
                  <Input id="collaborator_cnh" type="text" class="mt-1 block w-full" v-model="form.collaborator.cnh" v-mask="'###########'" />
                  <InputError class="mt-2" :message="form.errors['collaborator.cnh']" />
                </div>

                <div>
                  <Label for="collaborator_reservista">Certificado de Reservista</Label>
                  <Input id="collaborator_reservista" type="text" class="mt-1 block w-full" v-model="form.collaborator.reservista" />
                  <InputError class="mt-2" :message="form.errors['collaborator.reservista']" />
                </div>
                <div>
                  <Label for="collaborator_titulo_eleitor">Título de Eleitor</Label>
                  <Input id="collaborator_titulo_eleitor" type="text" class="mt-1 block w-full" v-model="form.collaborator.titulo_eleitor" />
                  <InputError class="mt-2" :message="form.errors['collaborator.titulo_eleitor']" />
                </div>
                <div>
                  <Label for="collaborator_zona_eleitoral">Zona Eleitoral</Label>
                  <Input id="collaborator_zona_eleitoral" type="text" class="mt-1 block w-full" v-model="form.collaborator.zona_eleitoral" />
                  <InputError class="mt-2" :message="form.errors['collaborator.zona_eleitoral']" />
                </div>
                <div>
                  <Label for="collaborator_pis_ctps_numero">PIS/PASEP ou Nº CTPS</Label>
                  <Input id="collaborator_pis_ctps_numero" type="text" class="mt-1 block w-full" v-model="form.collaborator.pis_ctps_numero" />
                  <InputError class="mt-2" :message="form.errors['collaborator.pis_ctps_numero']" />
                </div>
                <div>
                  <Label for="collaborator_ctps_serie">Série CTPS</Label>
                  <Input id="collaborator_ctps_serie" type="text" class="mt-1 block w-full" v-model="form.collaborator.ctps_serie" />
                  <InputError class="mt-2" :message="form.errors['collaborator.ctps_serie']" />
                </div>
                <div class="md:col-span-2">
                  <Label>Arquivos</Label>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <input id="collaborator_photo_file" ref="photoFileInput" type="file" class="hidden" @change="event => handleFileUpload(event, 'photo_file', photoPreview, true)" accept="image/*" />
                      <Button type="button" variant="outline" @click="photoFileInput?.click()" class="w-full bg-gray-100 justify-center">Foto (3x4)</Button>
                      <img v-if="photoPreview" :src="photoPreview" alt="Preview Foto" class="mt-2 h-24 rounded mx-auto"/>
                    </div>
                    <div>
                      <input id="collaborator_curriculum_file" ref="curriculumFileInput" type="file" class="hidden" @change="event => handleFileUpload(event, 'curriculum_file', curriculumFileName, false)" accept=".pdf,.doc,.docx" />
                      <Button type="button" variant="outline" @click="curriculumFileInput?.click()" class="w-full bg-gray-100 justify-center">Currículo</Button>
                      <p v-if="curriculumFileName" class="mt-2 text-sm text-gray-600 dark:text-gray-400 truncate text-center">{{ curriculumFileName }}</p>
                    </div>
                  </div>
                </div>
              </div>
              <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 pt-4">Dados Bancários</h4>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <Label for="collaborator_banco">Banco</Label>
                  <Input id="collaborator_banco" type="text" class="mt-1 block w-full" v-model="form.collaborator.banco" />
                  <InputError class="mt-2" :message="form.errors['collaborator.banco']" />
                </div>
                <div>
                  <Label for="collaborator_agencia">Agência</Label>
                  <Input id="collaborator_agencia" type="text" class="mt-1 block w-full" v-model="form.collaborator.agencia" />
                  <InputError class="mt-2" :message="form.errors['collaborator.agencia']" />
                </div>
                <div>
                  <Label for="collaborator_conta_corrente">Conta (com dígito)</Label>
                  <Input id="collaborator_conta_corrente" type="text" class="mt-1 block w-full" v-model="form.collaborator.conta_corrente" />
                  <InputError class="mt-2" :message="form.errors['collaborator.conta_corrente']" />
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
                <Button type="submit" variant="black" class="bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600" :disabled="form.processing" v-if="currentStep === totalSteps">Salvar Colaborador</Button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
