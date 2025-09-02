<script setup>
import { computed, ref, onMounted } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { useUIStore } from "@/stores/ui";

import {
  LayoutDashboard,
  UserCog,
  Store,
  Building2,
  Users,
  LogOut,
  ChevronDown,
  Menu,
  UsersRound,
  ChevronLeft,
  ChevronRight,
} from "lucide-vue-next";

import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";

import {
  Collapsible,
  CollapsibleContent,
  CollapsibleTrigger,
} from "@/Components/ui/collapsible";

const page = usePage();

// Controle da Sidebar usando Pinia
const uiStore = useUIStore();

// Inicializar sidebar ao montar
onMounted(() => {
  uiStore.initializeSidebar();
});

// Controle do menu Adm. Pessoas
const isAdmPessoasOpen = ref(false);

// Configurações dos links
const mainLinksConfig = [
  {
    name: "Dashboard",
    href: route("dashboard"),
    current: () => route().current("dashboard"),
    canAccess: (user) => !!user,
    icon: LayoutDashboard,
  },
];

const admPessoasSubLinksConfig = [
  {
    name: "Administradores",
    href: route("admins.index"),
    current: () => route().current("admins.*"),
    canAccess: (user) => user?.role === "admin",
    icon: UserCog,
  },
  {
    name: "Franqueados",
    href: route("franchises.index"),
    current: () => route().current("franchises.*"),
    canAccess: (user) => user?.role === "admin",
    icon: Store,
  },
  {
    name: "Clientes",
    href: route("clients.index"),
    current: () => route().current("clients.*"),
    canAccess: (user) =>
      user && (user.role === "admin" || user.role === "franchise"),
    icon: Building2,
  },
  {
    name: "Colaboradores",
    href: route("collaborators.index"),
    current: () => route().current("collaborators.*"),
    canAccess: (user) =>
      user &&
      (user.role === "admin" ||
        user.role === "franchise" ||
        user.role === "client"),
    icon: Users,
  },
];

// Computeds para processar os links baseados nas permissões do usuário
const currentUser = computed(() => page.props.auth.user);

const processedMainLinks = computed(() => {
  if (!currentUser.value) return [];
  return mainLinksConfig
    .filter((link) => link.canAccess(currentUser.value))
    .map((link) => ({ ...link, current: link.current() }));
});

const processedAdmPessoasSubLinks = computed(() => {
  if (!currentUser.value) return [];
  return admPessoasSubLinksConfig
    .filter((link) => link.canAccess(currentUser.value))
    .map((link) => ({ ...link, current: link.current() }));
});

const showAdmPessoasGroup = computed(
  () => processedAdmPessoasSubLinks.value.length > 0,
);

const isAdmPessoasGroupActive = computed(() => {
  return processedAdmPessoasSubLinks.value.some((link) => link.current);
});

// Abre o menu Adm. Pessoas se uma de suas rotas estiver ativa
isAdmPessoasOpen.value = isAdmPessoasGroupActive.value;
</script>

<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900 md:flex">
    <div
      v-if="uiStore.isMobileSidebarOpen"
      class="fixed inset-0 z-20 bg-black/50 transition-opacity md:hidden"
      @click="uiStore.closeMobileSidebar"
    ></div>

    <aside
      class="fixed inset-y-0 left-0 z-30 flex transform flex-col bg-gray-100 shadow-md transition-all duration-300 ease-in-out dark:bg-gray-800 md:relative md:translate-x-0"
      :class="{
        'translate-x-0': uiStore.isMobileSidebarOpen,
        '-translate-x-full': !uiStore.isMobileSidebarOpen,
        'w-64': !uiStore.isSidebarCollapsed,
        'md:w-16': uiStore.isSidebarCollapsed,
      }"
    >
      <div class="flex h-full flex-col">
        <div :class="uiStore.isSidebarCollapsed ? 'p-2' : 'p-4'">
          <Link
            :href="route('dashboard')"
            class="flex items-center justify-center mb-6"
            @click="uiStore.handleMobileLinkClick"
          >
            <ApplicationLogo v-if="!uiStore.isSidebarCollapsed" class="h-9 w-auto" />
            <img 
              v-if="uiStore.isSidebarCollapsed" 
              src="/logo-mobile.png" 
              alt="Logo"
              class="h-8 w-8"
            />
          </Link>

          <nav class="mt-5 flex-grow">
            <template v-for="item in processedMainLinks" :key="item.name">
              <Link
                :href="item.href"
                class="flex items-center mt-1 text-sm font-medium rounded-md transition-colors duration-150"
                :class="[
                  item.current
                    ? 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100'
                    : 'text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100',
                  uiStore.isSidebarCollapsed ? 'justify-center px-2 py-2' : 'px-3 py-2.5'
                ]"
                :title="uiStore.isSidebarCollapsed ? item.name : ''"
                @click="uiStore.handleMobileLinkClick"
              >
                <component :is="item.icon" :class="uiStore.isSidebarCollapsed ? 'h-5 w-5' : 'h-5 w-5 mr-3 flex-shrink-0'" />
                <span 
                  v-if="!uiStore.isSidebarCollapsed && uiStore.isTextVisible" 
                  class="whitespace-nowrap"
                >{{ item.name }}</span>
              </Link>
            </template>

            <div v-if="showAdmPessoasGroup && uiStore.isSidebarCollapsed" class="mt-1 space-y-1">
              <template
                v-for="item in processedAdmPessoasSubLinks"
                :key="item.name"
              >
                <Link
                  :href="item.href"
                  class="flex items-center justify-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-150"
                  :class="
                    item.current
                      ? 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100'
                      : 'text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100'
                  "
                  :title="item.name"
                  @click="uiStore.handleMobileLinkClick"
                >
                  <component :is="item.icon" class="h-5 w-5" />
                </Link>
              </template>
            </div>

            <Collapsible
              v-if="showAdmPessoasGroup && !uiStore.isSidebarCollapsed"
              v-model:open="isAdmPessoasOpen"
              class="mt-1"
            >
              <CollapsibleTrigger
                class="flex items-center justify-between w-full px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-150"
                :class="
                  isAdmPessoasGroupActive && !isAdmPessoasOpen
                    ? 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100'
                    : 'text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100'
                "
              >
                <div class="flex items-center">
                  <UsersRound class="h-5 w-5 mr-3 flex-shrink-0" />
                  <span 
                    v-if="uiStore.isTextVisible"
                    class="whitespace-nowrap"
                  >Adm. Pessoas</span>
                </div>
                <ChevronDown
                  class="h-4 w-4 transition-transform duration-200"
                  :class="[isAdmPessoasOpen && 'rotate-180']"
                />
              </CollapsibleTrigger>
              <CollapsibleContent
                class="pt-1 pl-4 border-l-2 border-gray-200 dark:border-gray-700 ml-[10px] mr-[-10px] space-y-px"
              >
                <template
                  v-for="item in processedAdmPessoasSubLinks"
                  :key="item.name"
                >
                  <Link
                    :href="item.href"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150"
                    :class="
                      item.current
                        ? 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100'
                        : 'text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-gray-200'
                    "
                    @click="handleLinkClick"
                  >
                    <component
                      :is="item.icon"
                      class="h-5 w-5 mr-2.5 flex-shrink-0"
                    />
                    <span 
                      v-if="uiStore.isTextVisible"
                      class="whitespace-nowrap"
                    >{{ item.name }}</span>
                  </Link>
                </template>
              </CollapsibleContent>
            </Collapsible>
          </nav>
        </div>
        <div :class="[uiStore.isSidebarCollapsed ? 'p-2' : 'p-4', 'mt-auto border-t border-gray-200 dark:border-gray-700 space-y-2']">
          <!-- Botão para colapsar sidebar quando expandida (apenas desktop) -->
          <button
            v-if="!uiStore.isSidebarCollapsed"
            @click="uiStore.collapseSidebar"
            class="hidden md:flex w-full items-center justify-between px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 rounded-md"
            title="Recolher menu"
          >
            <span class="whitespace-nowrap">Recolher menu</span>
            <ChevronLeft class="h-4 w-4" />
          </button>

          <!-- Botão para expandir sidebar quando recolhida (apenas desktop) -->
          <button
            v-if="uiStore.isSidebarCollapsed"
            @click="uiStore.expandSidebar"
            class="hidden md:flex w-full justify-center px-2 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 rounded-md"
            title="Expandir menu"
          >
            <ChevronRight class="h-5 w-5" />
          </button>
          
          <Link
            :href="route('logout')"
            method="post"
            as="button"
            class="w-full flex items-center text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100 rounded-md"
            :class="uiStore.isSidebarCollapsed ? 'justify-center px-2 py-2' : 'px-3 py-2.5'"
            :title="uiStore.isSidebarCollapsed ? 'Sair' : ''"
          >
            <LogOut :class="uiStore.isSidebarCollapsed ? 'h-5 w-5' : 'h-5 w-5 mr-3 flex-shrink-0'" />
            <span 
              v-if="!uiStore.isSidebarCollapsed && uiStore.isTextVisible" 
              class="whitespace-nowrap"
            >Sair</span>
          </Link>
        </div>
      </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
      <header
        v-if="$page.props.auth.user"
        class="bg-gray-100 dark:bg-gray-800 shadow"
      >
        <div class="w-full px-6 mx-auto flex justify-between items-center h-16">
          <button
            class="md:hidden p-2 rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700"
            @click="uiStore.toggleMobileSidebar"
          >
            <Menu class="h-6 w-6" />
          </button>

          <div class="hidden md:block">
            <slot name="header" />
          </div>

          <div class="flex items-center sm:ms-6">
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
                  <DropdownLink
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="flex items-center w-full text-left"
                  >
                    <LogOut class="h-4 w-4 mr-2" />
                    Sair
                  </DropdownLink>
                </template>
              </Dropdown>
            </div>
          </div>
        </div>
        <div class="md:hidden px-6 pt-2 pb-4">
          <slot name="header" />
        </div>
      </header>

      <main
        class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 dark:bg-gray-900 p-6"
      >
        <div
          v-if="$page.props.flash?.success"
          class="mb-4 p-4 rounded-md bg-green-100 dark:bg-green-800 text-sm font-medium text-green-700 dark:text-green-200"
        >
          {{ $page.props.flash.success }}
        </div>
        <div
          v-if="$page.props.flash?.error"
          class="mb-4 p-4 rounded-md bg-red-100 dark:bg-red-800 text-sm font-medium text-red-700 dark:text-red-200"
        >
          {{ $page.props.flash.error }}
        </div>
        <slot />
      </main>
    </div>
  </div>
</template>
