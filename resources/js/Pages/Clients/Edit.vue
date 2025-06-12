<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select';
import InputError from '@/Components/InputError.vue';
import { ref, computed } from 'vue';

const props = defineProps({
  client_data: Object,
  franchises: Array,
});

const page = usePage();
const userRole = computed(() => page.props.auth.user?.role);

const logoFileInput = ref(null);

const form = useForm({
  _method: 'PUT',
  name: props.client_data.user.name,
  email: props.client_data.user.email,
  password: '',
  password_confirmation: '',
  cnpj: props.client_data.cnpj,
  test_number: props.client_data.test_number,
  contract_end_date: props.client_data.contract_end_date_form,
  is_monthly_contract: props.client_data.is_monthly_contract,
  phone: props.client_data.phone,
  logo_file: null,
  franchise_id: props.client_data.franchise_id || null,
});

const logoPreview = ref(props.client_data.logo_full_url);

function handleLogoUpload(event) {
  const file = event.target.files[0];
  if (file) {
    form.logo_file = file;
    logoPreview.value = URL.createObjectURL(file);
  }
}

const submit = () => {
  form.transform(data => ({
    ...data,
    cnpj: data.cnpj.replace(/\D/g, ''),
    phone: data.phone.replace(/\D/g, ''),
  })).post(route('clients.update', props.client_data.id), {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      form.reset('password', 'password_confirmation');
      form.logo_file = null;
    },
  });
};
</script>

<template>
  <Head :title="'Editar Cliente: ' + client_data.user.name"/>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Editar Cliente: {{ client_data.user.name }}
      </h2>
    </template>

    <div class="mx-auto w-full">
        <div class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 md:p-8">
          <form @submit.prevent="submit" class="space-y-6">

            <div class="space-y-4">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b dark:border-gray-700 pb-2">Dados
                de Acesso</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div v-if="userRole === 'admin' && props.franchises && props.franchises.length > 0"
                     class="md:col-span-2">
                  <Label for="franchise_id">Franqueado Associado</Label>
                  <Select v-model="form.franchise_id">
                    <SelectTrigger id="franchise_id" class="bg-white">
                      <SelectValue placeholder="Selecione um franqueado"/>
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem v-for="franchise in props.franchises" :key="franchise.id" :value="franchise.id"
                                  class="cursor-pointer">{{ franchise.name }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
                  <InputError class="mt-2" :message="form.errors.franchise_id"/>
                </div>
                <div>
                  <Label for="user_name">Nome do Usuário</Label>
                  <Input id="user_name" type="text" class="mt-1 block w-full bg-white" v-model="form.name" required autofocus/>
                  <InputError class="mt-2" :message="form.errors.name"/>
                </div>
                <div>
                  <Label for="user_email">Email do Usuário</Label>
                  <Input id="user_email" type="email" class="mt-1 block w-full bg-white" v-model="form.email" required/>
                  <InputError class="mt-2" :message="form.errors.email"/>
                </div>
                <div>
                  <Label for="client_cnpj">CNPJ</Label>
                  <Input id="client_cnpj" type="text" class="mt-1 block w-full bg-white" v-model="form.cnpj"
                         v-mask="'##.###.###/####-##'" required/>
                  <InputError class="mt-2" :message="form.errors.cnpj"/>
                </div>
                <div>
                  <Label for="client_phone">Telefone</Label>
                  <Input id="client_phone" type="text" class="mt-1 block w-full bg-white" v-model="form.phone"
                         v-mask="['(##) ####-####', '(##) #####-####']"/>
                  <InputError class="mt-2" :message="form.errors.phone"/>
                </div>
                <div>
                  <Label for="client_test_number">Número de Teste</Label>
                  <Input id="client_test_number" type="text" class="mt-1 block w-full bg-white" v-model="form.test_number"/>
                  <InputError class="mt-2" :message="form.errors.test_number"/>
                </div>
                <div>
                  <Label for="client_contract_end_date">Data Final do Contrato</Label>
                  <Input id="client_contract_end_date" type="date" class="mt-1 block w-full bg-white"
                         v-model="form.contract_end_date"/>
                  <InputError class="mt-2" :message="form.errors.contract_end_date"/>
                </div>
                <div>
                  <Label>Novo Logo (opcional)</Label>
                  <div class="mt-1 flex items-center gap-4">
                    <input ref="logoFileInput" type="file" class="hidden" @change="handleLogoUpload" accept="image/*"/>
                    <Button type="button" variant="outline" class="bg-white" @click="logoFileInput?.click()">Escolher Arquivo</Button>
                    <img v-if="logoPreview" :src="logoPreview" alt="Preview Logo"
                         class="h-12 w-auto rounded-md object-cover"/>
                  </div>
                  <InputError class="mt-2" :message="form.errors.logo_file"/>
                </div>
                <div class="flex items-center space-x-2 self-end pb-1">
                  <Checkbox id="client_is_monthly_contract" :checked="form.is_monthly_contract"
                            @update:checked="form.is_monthly_contract = $event"/>
                  <Label for="client_is_monthly_contract">Contrato Mensal?</Label>
                  <InputError class="mt-2" :message="form.errors.is_monthly_contract"/>
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <Label for="user_password">Nova Senha</Label>
                  <Input id="user_password" type="password" class="mt-1 block w-full bg-white" v-model="form.password"/>
                  <InputError class="mt-2" :message="form.errors.password"/>
                </div>
                <div>
                  <Label for="user_password_confirmation">Confirmar Nova Senha</Label>
                  <Input id="user_password_confirmation" type="password" class="mt-1 block w-full bg-white"
                         v-model="form.password_confirmation"/>
                  <InputError class="mt-2" :message="form.errors.password_confirmation"/>
                </div>
              </div>
            </div>

            <div class="flex items-center justify-end mt-6 pt-6 border-t dark:border-gray-700">
              <Link :href="route('clients.index')" class="mr-4">
                <Button variant="outline" class="bg-white" type="button">Cancelar</Button>
              </Link>
              <Button type="submit" variant="black" class="bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600"
                      :disabled="form.processing">
                Atualizar Cliente
              </Button>
            </div>
          </form>
        </div>
      </div>
  </AuthenticatedLayout>
</template>
