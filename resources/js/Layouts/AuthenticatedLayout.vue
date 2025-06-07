<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

import {
  LayoutDashboard,
  UserCog,
  Store,
  Building2,
  Users,
  LogOut,
  CircleUserRound,
  UsersRound,
  ChevronDown,
} from 'lucide-vue-next';

import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

import {
  Collapsible,
  CollapsibleContent,
  CollapsibleTrigger,
} from '@/components/ui/collapsible';

const page = usePage();
const isAdmPessoasOpen = ref(false);

const mainLinksConfig = [
  {
    name: 'Dashboard',
    href: route('dashboard'),
    current: () => route().current('dashboard'),
    canAccess: (user) => !!user,
    icon: LayoutDashboard,
  },
];

const admPessoasSubLinksConfig = [
  {
    name: 'Administradores',
    href: route('admins.index'),
    current: () => route().current('admins.*'),
    canAccess: (user) => user?.role === 'admin',
    icon: UserCog,
  },
  {
    name: 'Franqueados',
    href: route('franchises.index'),
    current: () => route().current('franchises.*'),
    canAccess: (user) => user?.role === 'admin',
    icon: Store,
  },
  {
    name: 'Clientes',
    href: route('clients.index'),
    current: () => route().current('clients.*'),
    canAccess: (user) => user && (user.role === 'admin' || user.role === 'franchise'),
    icon: Building2,
  },
  {
    name: 'Colaboradores',
    href: route('collaborators.index'),
    current: () => route().current('collaborators.*'),
    canAccess: (user) => user && (user.role === 'admin' || user.role === 'franchise' || user.role === 'client'),
    icon: Users,
  },
];

const currentUser = computed(() => page.props.auth.user);

const processedMainLinks = computed(() => {
  if (!currentUser.value) return [];
  return mainLinksConfig
    .filter(link => link.canAccess(currentUser.value))
    .map(link => ({ ...link, current: link.current() }));
});

const processedAdmPessoasSubLinks = computed(() => {
  if (!currentUser.value) return [];
  return admPessoasSubLinksConfig
    .filter(link => link.canAccess(currentUser.value))
    .map(link => ({ ...link, current: link.current() }));
});

const showAdmPessoasGroup = computed(() => processedAdmPessoasSubLinks.value.length > 0);

const isAdmPessoasGroupActive = computed(() => {
  return processedAdmPessoasSubLinks.value.some(link => link.current);
});

isAdmPessoasOpen.value = isAdmPessoasGroupActive.value;

</script>

<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex">
    <aside class="w-64 bg-white dark:bg-gray-800 shadow-md flex-shrink-0 flex flex-col">
      <div class="p-4">
        <Link :href="route('dashboard')" class="flex items-center justify-center mb-6">
          <ApplicationLogo class="h-9 w-auto" />
        </Link>

        <nav class="mt-5 flex-grow">
          <template v-for="item in processedMainLinks" :key="item.name">
            <Link
              :href="item.href"
              class="flex items-center px-3 py-2.5 mt-1 text-sm font-medium rounded-md transition-colors duration-150"
              :class="item.current
                ? 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100'
                : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100'"
            >
              <component :is="item.icon" class="h-5 w-5 mr-3 flex-shrink-0"/>
              <span>{{ item.name }}</span>
            </Link>
          </template>

          <Collapsible v-if="showAdmPessoasGroup" v-model:open="isAdmPessoasOpen" class="mt-1">
            <CollapsibleTrigger
              class="flex items-center justify-between w-full px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-150"
              :class="isAdmPessoasGroupActive && !isAdmPessoasOpen
                ? 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100'
                : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100'"
            >
              <div class="flex items-center">
                <UsersRound class="h-5 w-5 mr-3 flex-shrink-0"/>
                <span>Adm. Pessoas</span>
              </div>
              <ChevronDown class="h-4 w-4 transition-transform duration-200"
                           :class="[isAdmPessoasOpen && 'rotate-180']"/>
            </CollapsibleTrigger>
            <CollapsibleContent
              class="pt-1 pl-4 border-l-2 border-gray-200 dark:border-gray-700 ml-[10px] mr-[-10px] space-y-px">
              <template v-for="item in processedAdmPessoasSubLinks" :key="item.name">
                <Link
                  :href="item.href"
                  class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150"
                  :class="item.current
                    ? 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100'
                    : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-gray-200'"
                >
                  <component :is="item.icon" class="h-4 w-4 mr-2.5 flex-shrink-0"/>
                  <span>{{ item.name }}</span>
                </Link>
              </template>
            </CollapsibleContent>
          </Collapsible>

        </nav>
      </div>
      <div class="p-4 mt-auto border-t border-gray-200 dark:border-gray-700">
        <Link
          :href="route('logout')"
          method="post"
          as="button"
          class="w-full flex items-center px-3 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100 rounded-md"
        >
          <LogOut class="h-5 w-5 mr-3 flex-shrink-0"/>
          <span>Logout</span>
        </Link>
      </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
      <header class="bg-white dark:bg-gray-800 shadow" v-if="$page.props.auth.user">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">
          <slot name="header"/>
          <div class="hidden sm:flex sm:items-center sm:ms-6">
            <div class="ms-3 relative">
              <Dropdown align="right" width="48">
                <template #trigger>
                  <span class="inline-flex rounded-md">
                    <button
                      type="button"
                      class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
                    >
                      {{ $page.props.auth.user.name }}
                      <svg
                        class="ms-2 -me-0.5 h-4 w-4"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </button>
                  </span>
                </template>
                <template #content>
                  <DropdownLink :href="route('profile.edit')" class="flex items-center">
                    <CircleUserRound class="h-4 w-4 mr-2"/>
                    Perfil
                  </DropdownLink>
                  <DropdownLink :href="route('logout')" method="post" as="button"
                                class="flex items-center w-full text-left">
                    <LogOut class="h-4 w-4 mr-2"/>
                    Logout
                  </DropdownLink>
                </template>
              </Dropdown>
            </div>
          </div>
        </div>
      </header>

      <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900 p-6">
        <slot/>
      </main>
    </div>
  </div>
</template>
