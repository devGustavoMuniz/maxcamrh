<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import InputError from '@/Components/InputError.vue';
import { ref } from 'vue';

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  cnpj: '',
  test_number: '',
  contract_end_date: '',
  is_monthly_contract: false,
  phone: '',
  logo_file: null,
});

const logoPreview = ref(null);

function handleLogoUpload(event) {
  const file = event.target.files[0];
  if (file) {
    form.logo_file = file; // Atribui o objeto File ao form
    logoPreview.value = URL.createObjectURL(file); // Gera URL para preview
  } else {
    form.logo_file = null;
    logoPreview.value = null;
  }
}

const submit = () => {
  form.post(route('clients.store'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
    onSuccess: () => {
      form.reset();
      logoPreview.value = null;
    },
  });
};
</script>

<template>
  <Head title="Adicionar Cliente" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Adicionar Novo Cliente
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
          <form @submit.prevent="submit" class="space-y-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Dados do Usuário (para o Cliente)</h3>
            <div>
              <Label for="user_name">Nome do Usuário</Label>
              <Input id="user_name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
              <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <div>
              <Label for="user_email">Email do Usuário</Label>
              <Input id="user_email" type="email" class="mt-1 block w-full" v-model="form.email" required />
              <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div>
              <Label for="user_password">Senha</Label>
              <Input id="user_password" type="password" class="mt-1 block w-full" v-model="form.password" required />
              <InputError class="mt-2" :message="form.errors.password" />
            </div>
            <div>
              <Label for="user_password_confirmation">Confirmar Senha</Label>
              <Input id="user_password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" required />
              <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <hr class="my-6 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Dados da Empresa Cliente</h3>
            <div>
              <Label for="client_cnpj">CNPJ</Label>
              <Input id="client_cnpj" type="text" class="mt-1 block w-full" v-model="form.cnpj" required />
              <InputError class="mt-2" :message="form.errors.cnpj" />
            </div>
            <div>
              <Label for="client_phone">Telefone</Label>
              <Input id="client_phone" type="text" class="mt-1 block w-full" v-model="form.phone" />
              <InputError class="mt-2" :message="form.errors.phone" />
            </div>
            <div>
              <Label for="client_test_number">Número de Teste</Label>
              <Input id="client_test_number" type="text" class="mt-1 block w-full" v-model="form.test_number" />
              <InputError class="mt-2" :message="form.errors.test_number" />
            </div>
            <div>
              <Label for="client_contract_end_date">Data Final do Contrato</Label>
              <Input id="client_contract_end_date" type="date" class="mt-1 block w-full" v-model="form.contract_end_date" />
              <InputError class="mt-2" :message="form.errors.contract_end_date" />
            </div>
            <div class="flex items-center space-x-2 mt-1">
              <Checkbox id="client_is_monthly_contract" v-model:checked="form.is_monthly_contract" />
              <Label for="client_is_monthly_contract" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                Contrato Mensal?
              </Label>
              <InputError class="mt-2" :message="form.errors.is_monthly_contract" />
            </div>
            <div>
              <Label for="client_logo_file">Logo da Empresa</Label>
              <Input
                id="client_logo_file"
                type="file"
                class="mt-1 block w-full file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                @input="handleLogoUpload"
              />
              <InputError class="mt-2" :message="form.errors.logo_file" />
              <div v-if="logoPreview" class="mt-2">
                <img :src="logoPreview" alt="Preview do Logo" class="h-20 w-auto rounded" />
              </div>
            </div>

            <div class="flex items-center justify-end mt-6 pt-6 border-t dark:border-gray-700">
              <Link :href="route('clients.index')" class="mr-4">
                <Button variant="outline" type="button">Cancelar</Button>
              </Link>
              <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Salvar Cliente
              </Button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
