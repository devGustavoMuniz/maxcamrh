<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";

import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/Components/ui/card";

import { Loader2 } from "lucide-vue-next";

const form = useForm({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

const submit = () => {
  form.post(route("register"), {
    onFinish: () => form.reset("password", "password_confirmation"),
  });
};
</script>

<template>
  <GuestLayout>
    <Head title="Cadastrar" />

    <Card class="w-full max-w-md mx-auto">
      <CardHeader class="text-center">
        <CardTitle class="text-2xl">Criar Nova Conta</CardTitle>
        <CardDescription>
          Preencha os campos abaixo para se registrar.
        </CardDescription>
      </CardHeader>
      <CardContent>
        <form class="space-y-6" @submit.prevent="submit">
          <div>
            <Label for="name">Nome</Label>
            <Input
              id="name"
              v-model="form.name"
              type="text"
              class="mt-1 block w-full bg-white"
              required
              autofocus
              autocomplete="name"
              placeholder="Seu nome completo"
            />
            <InputError class="mt-2" :message="form.errors.name" />
          </div>

          <div>
            <Label for="email">E-mail</Label>
            <Input
              id="email"
              v-model="form.email"
              type="email"
              class="mt-1 block w-full bg-white"
              required
              autocomplete="username"
              placeholder="seuemail@exemplo.com"
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
              autocomplete="new-password"
              placeholder="Mínimo 8 caracteres"
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
              autocomplete="new-password"
              placeholder="Repita a senha"
            />
            <InputError
              class="mt-2"
              :message="form.errors.password_confirmation"
            />
          </div>

          <Button
            type="submit"
            class="w-full bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          >
            <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
            Cadastrar
          </Button>
        </form>
      </CardContent>
      <CardFooter class="flex justify-center pt-4">
        <Link
          :href="route('login')"
          class="text-sm text-muted-foreground hover:text-primary underline-offset-4 hover:underline"
        >
          Já possui uma conta? Entrar
        </Link>
      </CardFooter>
    </Card>
  </GuestLayout>
</template>
