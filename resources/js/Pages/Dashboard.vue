<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

import { Button } from '@/components/ui/button';
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card';

import {
  Users,
  Store,
  Building2,
  UserCog,
  Briefcase,
  Settings2,
  LayoutDashboard
} from 'lucide-vue-next';

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({}),
  },
});

const currentUser = computed(() => usePage().props.auth.user);
const userRole = computed(() => currentUser.value?.role);

</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center">
        <LayoutDashboard class="h-6 w-6 mr-2 text-gray-700 dark:text-gray-300" />
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
          Dashboard
        </h2>
      </div>
    </template>

    <div class="mx-auto w-full">
        <div class="mb-8">
          <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
            Bem-vindo(a) de volta, {{ currentUser?.name }}!
          </h1>
          <p class="text-gray-600 dark:text-gray-400">
            Aqui está um resumo das suas atividades e acessos rápidos.
          </p>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

          <template v-if="userRole === 'admin'">
            <Card>
              <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle class="text-sm font-medium">Administradores</CardTitle>
                <UserCog class="h-5 w-5 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div class="text-2xl font-bold">{{ props.stats?.totalAdmins || 'N/A' }}</div>
                <p class="text-xs text-muted-foreground">Usuários com perfil de admin</p>
              </CardContent>
              <CardFooter>
                <Link :href="route('admins.index')" class="w-full">
                  <Button variant="outline" class="w-full bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">Gerenciar Admins</Button>
                </Link>
              </CardFooter>
            </Card>
            <Card>
              <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle class="text-sm font-medium">Franqueados</CardTitle>
                <Store class="h-5 w-5 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div class="text-2xl font-bold">{{ props.stats?.totalFranchises || 'N/A' }}</div>
                <p class="text-xs text-muted-foreground">Total de unidades franqueadas</p>
              </CardContent>
              <CardFooter>
                <Link :href="route('franchises.index')" class="w-full">
                  <Button variant="outline" class="w-full bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">Gerenciar Franqueados</Button>
                </Link>
              </CardFooter>
            </Card>
            <Card>
              <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle class="text-sm font-medium">Clientes</CardTitle>
                <Building2 class="h-5 w-5 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div class="text-2xl font-bold">{{ props.stats?.totalClients || 'N/A' }}</div>
                <p class="text-xs text-muted-foreground">Total de clientes ativos</p>
              </CardContent>
              <CardFooter>
                <Link :href="route('clients.index')" class="w-full">
                  <Button variant="outline" class="w-full bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">Gerenciar Clientes</Button>
                </Link>
              </CardFooter>
            </Card>
            <Card>
              <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle class="text-sm font-medium">Colaboradores</CardTitle>
                <Users class="h-5 w-5 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div class="text-2xl font-bold">{{ props.stats?.totalCollaborators || 'N/A' }}</div>
                <p class="text-xs text-muted-foreground">Total de colaboradores registrados</p>
              </CardContent>
              <CardFooter>
                <Link :href="route('collaborators.index')" class="w-full">
                  <Button variant="outline" class="w-full bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">Gerenciar Colaboradores</Button>
                </Link>
              </CardFooter>
            </Card>
          </template>

          <template v-if="userRole === 'franchise'">
            <Card>
              <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle class="text-sm font-medium">Meus Clientes</CardTitle>
                <Building2 class="h-5 w-5 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div class="text-2xl font-bold">{{ props.stats?.myTotalClients || 'N/A' }}</div>
                <p class="text-xs text-muted-foreground">Clientes vinculados à sua franquia</p>
              </CardContent>
              <CardFooter>
                <Link :href="route('clients.index')" class="w-full">
                  <Button variant="outline" class="w-full bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">Gerenciar Meus Clientes</Button>
                </Link>
              </CardFooter>
            </Card>
            <Card>
              <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle class="text-sm font-medium">Colaboradores (dos Clientes)</CardTitle>
                <Users class="h-5 w-5 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div class="text-2xl font-bold">{{ props.stats?.myTotalCollaborators || 'N/A' }}</div>
                <p class="text-xs text-muted-foreground">Colaboradores dos seus clientes</p>
              </CardContent>
              <CardFooter>
                <Link :href="route('collaborators.index')" class="w-full">
                  <Button variant="outline" class="w-full bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">Gerenciar Colaboradores</Button>
                </Link>
              </CardFooter>
            </Card>
          </template>

          <template v-if="userRole === 'client'">
            <Card>
              <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle class="text-sm font-medium">Meus Colaboradores</CardTitle>
                <Users class="h-5 w-5 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div class="text-2xl font-bold">{{ props.stats?.myCompanyCollaborators || 'N/A' }}</div>
                <p class="text-xs text-muted-foreground">Colaboradores da sua empresa</p>
              </CardContent>
              <CardFooter>
                <Link :href="route('collaborators.index')" class="w-full">
                  <Button variant="outline" class="w-full bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">Gerenciar Meus Colaboradores</Button>
                </Link>
              </CardFooter>
            </Card>
          </template>

          <template v-if="userRole === 'collaborator'">
            <Card class="md:col-span-2"> {/* Ocupa mais espaço se for o único card */}
              <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle class="text-sm font-medium">Meu Perfil</CardTitle>
                <Settings2 class="h-5 w-5 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <p class="text-sm text-muted-foreground">Acesse e atualize suas informações pessoais e profissionais.</p>
                {/* Aqui você poderia exibir alguns dados rápidos do colaborador se passados via props */}
                {/* Ex: <p>Cargo: {{ currentUser.details?.position || 'Não informado' }} </p> */}
              </CardContent>
              <CardFooter>
                <Link :href="route('profile.edit')" class="w-full">
                  <Button variant="outline" class="w-full bg-gray-800 text-white hover:bg-gray-700 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">Editar Meu Perfil</Button>
                </Link>
              </CardFooter>
            </Card>
          </template>

          <template v-if="!userRole">
            <Card>
              <CardContent class="p-6">
                <p class="text-gray-700 dark:text-gray-300">Nenhuma informação específica para exibir no dashboard para seu perfil.</p>
              </CardContent>
            </Card>
          </template>
        </div>

      </div>
  </AuthenticatedLayout>
</template>
