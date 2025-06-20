<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";

import { Button } from "@/Components/ui/button";
import { Checkbox } from "@/Components/ui/checkbox";
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

defineProps({
  canResetPassword: {
    type: Boolean,
  },
  status: {
    type: String,
  },
});

const form = useForm({
  email: "",
  password: "",
  remember: false,
});

const submit = () => {
  form.post(route("login"), {
    onFinish: () => form.reset("password"),
  });
};
</script>

<template>
  <GuestLayout>
    <Head title="Entrar" />

    <Card class="w-full max-w-md mx-auto">
      <CardHeader class="text-center">
        <CardDescription>
          Bem-vindo de volta! Digite seus dados para continuar.
        </CardDescription>
      </CardHeader>
      <CardContent>
        <div
          v-if="status"
          class="mb-4 text-sm font-medium text-green-600 dark:text-green-400"
        >
          {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
          <div>
            <Label for="email">Email</Label>
            <Input
              id="email"
              type="email"
              class="mt-1 block w-full bg-white"
              v-model="form.email"
              required
              autofocus
              autocomplete="username"
              placeholder="seuemail@exemplo.com"
            />
            <InputError class="mt-2" :message="form.errors.email" />
          </div>

          <div>
            <div class="flex items-center justify-between">
              <Label for="password">Senha</Label>
              <Link
                v-if="canResetPassword"
                :href="route('password.request')"
                class="text-sm text-muted-foreground hover:text-primary underline-offset-4 hover:underline"
              >
                Esqueceu sua senha?
              </Link>
            </div>
            <Input
              id="password"
              type="password"
              class="mt-1 block w-full bg-white"
              v-model="form.password"
              required
              autocomplete="current-password"
              placeholder="********"
            />
            <InputError class="mt-2" :message="form.errors.password" />
          </div>

          <div class="flex items-center space-x-2">
            <Checkbox id="remember" v-model:checked="form.remember" />
            <Label
              for="remember"
              class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
            >
              Lembrar-me
            </Label>
          </div>

          <Button
            type="submit"
            class="w-full bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600"
            :disabled="form.processing"
          >
            <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
            Entrar
          </Button>
        </form>
      </CardContent>
      <CardFooter class="flex flex-col items-center space-y-2 pt-4">
        <p class="text-sm text-muted-foreground">
          Não tem uma conta?
          <Link
            :href="route('register')"
            class="font-semibold text-primary hover:underline"
          >
            Cadastre-se
          </Link>
        </p>
      </CardFooter>
    </Card>
  </GuestLayout>
</template>
