<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import FranchiseForm from "@/Components/Franchise/FranchiseForm.vue";

const form = useForm({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
  maxcam_email: "",
  cnpj: "",
  max_client: 0,
  contract_start_date: "",
  actuation_region: "",
  document_file: null,
  observations: "",
});

const submit = () => {
  form
    .transform((data) => ({
      ...data,
      cnpj: data.cnpj.replace(/\D/g, ""),
    }))
    .post(route("franchises.store"), {
      onSuccess: () => {
        form.reset();
      },
    });
};
</script>

<template>
  <Head title="Adicionar Franqueado" />
  <AuthenticatedLayout>
    <template #header>
      <h2
        class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
      >
        Adicionar Novo Franqueado
      </h2>
    </template>

    <div class="mx-auto w-full">
      <div
        class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 md:p-8"
      >
        <form class="space-y-6" @submit.prevent="submit">
          <FranchiseForm :franchise-form="form" />

          <div
            class="flex items-center justify-end mt-6 pt-6 border-t dark:border-gray-700"
          >
            <Link :href="route('franchises.index')" class="mr-4">
              <Button variant="outline" class="bg-white" type="button"
                >Cancelar</Button
              >
            </Link>
            <Button
              type="submit"
              class="bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600"
              :disabled="form.processing"
            >
              Salvar Franqueado
            </Button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
