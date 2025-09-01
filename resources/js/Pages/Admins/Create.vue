<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import InputError from "@/Components/InputError.vue";

const form = useForm({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

const submit = () => {
  form.post(route("admins.store"), {
    onFinish: () => form.reset("password", "password_confirmation"),
  });
};
</script>

<template>
  <Head title="Adicionar Administrador" />

  <AuthenticatedLayout>
    <template #header>
      <h2
        class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
      >
        Adicionar Novo Administrador
      </h2>
    </template>

    <div class="mx-auto w-full">
      <div
        class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 md:p-8"
      >
        <form class="space-y-6" @submit.prevent="submit">
          <h3
            class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b dark:border-gray-700 pb-2"
          >
            Dados de Acesso do Administrador
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <Label for="name">Nome</Label>
              <Input
                id="name"
                v-model="form.name"
                type="text"
                class="mt-1 block w-full bg-white"
                required
                autofocus
              />
              <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
              <Label for="email">Email</Label>
              <Input
                id="email"
                v-model="form.email"
                type="email"
                class="mt-1 block w-full bg-white"
                required
              />
              <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
              <Label for="password">Senha</Label>
              <Input
                id="password"
                v-model="form.password"
                type="password"
                class="mt-1 block w-full bg-white"
                required
              />
              <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
              <Label for="password_confirmation">Confirmar Senha</Label>
              <Input
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                class="mt-1 block w-full bg-white"
                required
              />
              <InputError
                class="mt-2"
                :message="form.errors.password_confirmation"
              />
            </div>
          </div>

          <div
            class="flex items-center justify-end mt-6 pt-6 border-t dark:border-gray-700"
          >
            <Link :href="route('admins.index')" class="mr-4">
              <Button variant="outline" class="bg-white" type="button"
                >Cancelar</Button
              >
            </Link>
            <Button
              type="submit"
              variant="black"
              class="bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600"
              :disabled="form.processing"
            >
              Salvar Administrador
            </Button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
