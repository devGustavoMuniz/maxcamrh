import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useUIStore = defineStore('ui', () => {
  // Estado da sidebar
  const sidebarCollapsed = ref(false)
  const sidebarTextVisible = ref(true)
  const mobileSidebarOpen = ref(false)

  // Computeds para reatividade
  const isSidebarCollapsed = computed(() => sidebarCollapsed.value)
  const isTextVisible = computed(() => sidebarTextVisible.value)
  const isMobileSidebarOpen = computed(() => mobileSidebarOpen.value)

  // Inicializar estado do localStorage
  const initializeSidebar = () => {
    if (typeof window !== 'undefined') {
      const saved = localStorage.getItem('sidebar-collapsed')
      if (saved !== null) {
        const collapsed = JSON.parse(saved)
        sidebarCollapsed.value = collapsed
        sidebarTextVisible.value = !collapsed
      }
    }
  }

  // Persistir no localStorage
  const persistSidebarState = () => {
    if (typeof window !== 'undefined') {
      localStorage.setItem('sidebar-collapsed', JSON.stringify(sidebarCollapsed.value))
    }
  }

  // Actions para controlar a sidebar
  const toggleSidebar = () => {
    if (sidebarCollapsed.value) {
      expandSidebar()
    } else {
      collapseSidebar()
    }
  }

  const collapseSidebar = () => {
    sidebarTextVisible.value = false
    setTimeout(() => {
      sidebarCollapsed.value = true
      persistSidebarState()
    }, 50)
  }

  const expandSidebar = () => {
    sidebarCollapsed.value = false
    persistSidebarState()
    setTimeout(() => {
      sidebarTextVisible.value = true
    }, 200)
  }

  // Actions para sidebar mobile
  const openMobileSidebar = () => {
    mobileSidebarOpen.value = true
  }

  const closeMobileSidebar = () => {
    mobileSidebarOpen.value = false
  }

  const toggleMobileSidebar = () => {
    mobileSidebarOpen.value = !mobileSidebarOpen.value
  }

  // Action para lidar com cliques em links mobile
  const handleMobileLinkClick = () => {
    if (typeof window !== 'undefined' && window.innerWidth < 768) {
      closeMobileSidebar()
    }
  }

  return {
    // State
    sidebarCollapsed,
    sidebarTextVisible,
    mobileSidebarOpen,
    
    // Getters
    isSidebarCollapsed,
    isTextVisible,
    isMobileSidebarOpen,
    
    // Actions
    initializeSidebar,
    toggleSidebar,
    collapseSidebar,
    expandSidebar,
    openMobileSidebar,
    closeMobileSidebar,
    toggleMobileSidebar,
    handleMobileLinkClick
  }
})