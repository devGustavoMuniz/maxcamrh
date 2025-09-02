<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";

import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/Components/ui/card";

import { Loader2 } from "lucide-vue-next";

defineProps({
  status: {
    type: String,
    default: "",
  },
});

const form = useForm({
  email: "",
});

const submit = () => {
  form.post(route("password.email"));
};
</script>

<template>
  <GuestLayout>
    <Head title="Esqueceu a Senha" />

    <Card class="w-full max-w-md mx-auto">
      <CardHeader class="text-center">
        <CardTitle class="text-2xl">Redefinir Senha</CardTitle>
        <CardDescription>
          Esqueceu sua senha? Sem problemas. Informe seu endereço de e-mail e
          enviaremos um link para redefinir sua senha, permitindo que você
          escolha uma nova.
        </CardDescription>
      </CardHeader>
      <CardContent>
        <div
          v-if="status"
          class="mb-4 text-sm font-medium text-green-600 dark:text-green-400"
        >
          {{ status }}
        </div>

        <form class="space-y-6" @submit.prevent="submit">
          <div>
            <Label for="email">E-mail</Label>
            <Input
              id="email"
              v-model="form.email"
              type="email"
              class="mt-1 block w-full"
              required
              autofocus
              autocomplete="username"
              placeholder="seuemail@exemplo.com"
            />
            <InputError class="mt-2" :message="form.errors.email" />
          </div>

          <Button
            type="submit"
            class="bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 w-full"
            :disabled="form.processing"
          >
            <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
            Enviar Link de Redefinição de Senha
          </Button>
        </form>
      </CardContent>
    </Card>
  </GuestLayout>
</template>
