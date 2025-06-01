<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'

const props = defineProps({
  collaborator_data: Object,
});

function getDetail(value, fallback = 'N/A') {
  return value || fallback;
}
</script>

<template>
  <Head :title="'Detalhes do Colaborador: ' + collaborator_data.user?.name" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Detalhes do Colaborador: {{ collaborator_data.user?.name }}
        </h2>
        <div>
          <Link :href="route('collaborators.edit', collaborator_data.id)" class="mr-2">
            <Button variant="outline">Editar</Button>
          </Link>
          <Link :href="route('collaborators.index')">
            <Button variant="default">Voltar para a Lista</Button>
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
          <div class="flex items-start space-x-6">
            <Avatar class="h-24 w-24 border">
              <AvatarImage :src="collaborator_data.photo_full_url" :alt="collaborator_data.user?.name" />
              <AvatarFallback>{{ collaborator_data.user?.name?.substring(0,2).toUpperCase() || 'C' }}</AvatarFallback>
            </Avatar>
            <div>
              <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ getDetail(collaborator_data.user?.name) }}</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400">{{ getDetail(collaborator_data.user?.email) }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-500">ID do Usuário: {{ getDetail(collaborator_data.user?.id) }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-500">Colaborador ID: {{ getDetail(collaborator_data.id) }}</p>
              <div v-if="collaborator_data.curriculum_full_url" class="mt-3">
                <a :href="collaborator_data.curriculum_full_url" target="_blank" class="text-sm text-blue-600 hover:underline dark:text-blue-400">
                  Ver Currículo
                </a>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
          <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100 border-b pb-2">Dados Pessoais</h3>
          <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
            <div><dt class="detail-dt">Data de Nascimento:</dt><dd class="detail-dd">{{ collaborator_data.date_of_birth ? new Date(collaborator_data.date_of_birth).toLocaleDateString('pt-BR', {timeZone: 'UTC'}) : 'N/A' }}</dd></div>
            <div><dt class="detail-dt">Gênero:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.gender) }}</dd></div>
            <div><dt class="detail-dt">PNE:</dt><dd class="detail-dd">{{ collaborator_data.is_special_needs_person ? 'Sim' : 'Não' }}</dd></div>
            <div><dt class="detail-dt">Estado Civil:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.marital_status) }}</dd></div>
            <div><dt class="detail-dt">Escolaridade:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.scholarity) }}</dd></div>
            <div><dt class="detail-dt">Nome do Pai:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.father_name) }}</dd></div>
            <div><dt class="detail-dt">Nome da Mãe:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.mother_name) }}</dd></div>
            <div><dt class="detail-dt">Nacionalidade:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.nationality) }}</dd></div>
          </dl>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
          <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100 border-b pb-2">Contato e Endereço</h3>
          <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
            <div><dt class="detail-dt">Email Pessoal:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.personal_email) }}</dd></div>
            <div><dt class="detail-dt">Email Comercial:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.business_email) }}</dd></div>
            <div><dt class="detail-dt">Telefone Fixo:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.phone) }}</dd></div>
            <div><dt class="detail-dt">Celular:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.cellphone) }}</dd></div>
            <div><dt class="detail-dt">Tel. Emergência:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.emergency_phone) }}</dd></div>
          </dl>
          <div v-if="collaborator_data.address" class="mt-4 pt-4 border-t dark:border-gray-600">
            <h4 class="text-md font-medium mb-2 text-gray-700 dark:text-gray-300">Endereço Principal:</h4>
            <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
              <div><dt class="detail-dt">CEP:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.address.cep) }}</dd></div>
              <div><dt class="detail-dt">Logradouro:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.address.street) }}</dd></div>
              <div><dt class="detail-dt">Número:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.address.number) }}</dd></div>
              <div><dt class="detail-dt">Complemento:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.address.complement) }}</dd></div>
              <div><dt class="detail-dt">Bairro:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.address.neighborhood) }}</dd></div>
              <div><dt class="detail-dt">Cidade:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.address.city) }}</dd></div>
              <div><dt class="detail-dt">Estado:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.address.state) }}</dd></div>
            </dl>
          </div>
          <p v-else class="mt-4 text-sm text-gray-500 dark:text-gray-400">Nenhum endereço principal cadastrado.</p>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
          <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100 border-b pb-2">Dados Contratuais</h3>
          <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
            <div><dt class="detail-dt">Departamento:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.department) }}</dd></div>
            <div><dt class="detail-dt">Cargo:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.position) }}</dd></div>
            <div><dt class="detail-dt">Tipo de Contrato:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.type_of_contract) }}</dd></div>
            <div><dt class="detail-dt">Salário:</dt><dd class="detail-dd">R$ {{ collaborator_data.salary ? parseFloat(collaborator_data.salary).toFixed(2).replace('.',',') : 'N/A' }}</dd></div>
            <div><dt class="detail-dt">Data de Admissão:</dt><dd class="detail-dd">{{ collaborator_data.admission_date ? new Date(collaborator_data.admission_date).toLocaleDateString('pt-BR', {timeZone: 'UTC'}) : 'N/A' }}</dd></div>
            <div><dt class="detail-dt">Início Efetivo Contrato:</dt><dd class="detail-dd">{{ collaborator_data.contract_start_date ? new Date(collaborator_data.contract_start_date).toLocaleDateString('pt-BR', {timeZone: 'UTC'}) : 'N/A' }}</dd></div>
            <div><dt class="detail-dt">Fim Contrato:</dt><dd class="detail-dd">{{ collaborator_data.contract_expiration ? new Date(collaborator_data.contract_expiration).toLocaleDateString('pt-BR', {timeZone: 'UTC'}) : 'N/A' }}</dd></div>
            <div><dt class="detail-dt">Superior Direto:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.direct_superior_name) }}</dd></div>
            <div><dt class="detail-dt">Grau Hierárquico:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.hierarchical_degree) }}</dd></div>
            <div class="md:col-span-2 lg:col-span-3"><dt class="detail-dt">Observações Contratuais:</dt><dd class="detail-dd whitespace-pre-wrap">{{ getDetail(collaborator_data.observations) }}</dd></div>
          </dl>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
          <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100 border-b pb-2">Documentos e Dados Bancários</h3>
          <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
            <div><dt class="detail-dt">CPF:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.cpf) }}</dd></div>
            <div><dt class="detail-dt">RG:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.rg) }}</dd></div>
            <div><dt class="detail-dt">CNH:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.cnh) }}</dd></div>
            <div><dt class="detail-dt">Reservista:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.reservista) }}</dd></div>
            <div><dt class="detail-dt">Título de Eleitor:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.titulo_eleitor) }}</dd></div>
            <div><dt class="detail-dt">Zona Eleitoral:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.zona_eleitoral) }}</dd></div>
            <div><dt class="detail-dt">PIS/CTPS Nº:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.pis_ctps_numero) }}</dd></div>
            <div><dt class="detail-dt">CTPS Série:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.ctps_serie) }}</dd></div>
            <hr class="md:col-span-2 lg:col-span-3 my-2 dark:border-gray-700">
            <div><dt class="detail-dt">Banco:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.banco) }}</dd></div>
            <div><dt class="detail-dt">Agência:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.agencia) }}</dd></div>
            <div><dt class="detail-dt">Conta Corrente:</dt><dd class="detail-dd">{{ getDetail(collaborator_data.conta_corrente) }}</dd></div>
          </dl>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
          <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100 border-b pb-2">Datas do Sistema</h3>
          <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
            <div><dt class="detail-dt">Colaborador Criado em:</dt><dd class="detail-dd">{{ collaborator_data.created_at }}</dd></div>
            <div><dt class="detail-dt">Última Atualização (Colab.):</dt><dd class="detail-dd">{{ collaborator_data.updated_at }}</dd></div>
          </dl>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.detail-dt {
  @apply text-sm font-medium text-gray-500 dark:text-gray-400;
}
.detail-dd {
  @apply mt-1 text-sm text-gray-900 dark:text-gray-100 break-words;
}
</style>
