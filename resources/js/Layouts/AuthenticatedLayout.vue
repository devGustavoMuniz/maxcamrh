<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
// Você pode querer importar ícones para os links da sidebar, ex: lucide-vue-next
// import { HomeIcon, UsersIcon, BriefcaseIcon, UserCheckIcon, UserCogIcon } from 'lucide-vue-next';

// Para o dropdown de usuário (se mantiver o do Breeze)
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue'; // Pode ser útil para links da sidebar também
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

const showingNavigationDropdown = ref(false);

const sidebarLinks = ref([
  { name: 'Dashboard', href: route('dashboard'), /* icon: HomeIcon, */ current: route().current('dashboard') },
  { name: 'Administradores', href: route('admins.index'), /* icon: UserCogIcon, */ current: false }, // Ex: route('admins.index')
  { name: 'Franqueados', href: '#', /* icon: BriefcaseIcon, */ current: false }, // Ex: route('franchises.index')
  { name: 'Clientes', href: '#', /* icon: UsersIcon, */ current: false },       // Ex: route('clients.index')
  { name: 'Colaboradores', href: '#', /* icon: UserCheckIcon, */ current: false }, // Ex: route('colaborators.index')
]);

</script>

<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex">
    <aside class="w-64 bg-white dark:bg-gray-800 shadow-md flex-shrink-0">
      <div class="p-4">
        <Link :href="route('dashboard')" class="block text-center mb-6">
          <ApplicationLogo class="h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
        </Link>

        <nav class="mt-5">
          <template v-for="item in sidebarLinks" :key="item.name">
            <Link
              :href="item.href"
              class="flex items-center px-4 py-2 mt-2 text-sm font-medium rounded-md transition-colors duration-150"
              :class="item.current ? 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100'"
            >
              {{ item.name }}
            </Link>
          </template>
        </nav>
      </div>
      <div class="p-4 mt-auto border-t border-gray-200 dark:border-gray-700">
        <Link
          :href="route('logout')"
          method="post"
          as="button"
          class="w-full flex items-center px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100 rounded-md"
        >
          Logout
        </Link>
      </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
      <header class="bg-white dark:bg-gray-800 shadow" v-if="$page.props.auth.user">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
          <slot name="header" />

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
                  <DropdownLink :href="route('profile.edit')"> Perfil </DropdownLink>
                  <DropdownLink :href="route('logout')" method="post" as="button">
                    Logout
                  </DropdownLink>
                </template>
              </Dropdown>
            </div>
          </div>
        </div>
      </header>

      <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900 p-6">
        <slot /> </main>
    </div>
  </div>
</template>
