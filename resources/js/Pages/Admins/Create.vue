<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/Components/InputError.vue'; // Do Breeze, ou crie o seu

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.post(route('admins.store'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
};
</script>

<template>
  <Head title="Adicionar Administrador" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Adicionar Novo Administrador
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
          <form @submit.prevent="submit">
            <div>
              <Label for="name">Nome</Label>
              <Input id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
              <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
              <Label for="email">Email</Label>
              <Input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
              <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
              <Label for="password">Senha</Label>
              <Input id="password" type="password" class="mt-1 block w-full" v-model="form.password" required />
              <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
              <Label for="password_confirmation">Confirmar Senha</Label>
              <Input id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" required />
              <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="flex items-center justify-end mt-6">
              <Link :href="route('admins.index')" class="mr-4">
                <Button variant="outline">Cancelar</Button>
              </Link>
              <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Salvar Administrador
              </Button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
