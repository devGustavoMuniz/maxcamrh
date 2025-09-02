import { useUIStore } from '@/stores/ui'
import { onMounted, computed } from 'vue'

export function useSidebar() {
  const uiStore = useUIStore()

  // Inicializar sidebar ao montar o componente
  onMounted(() => {
    uiStore.initializeSidebar()
  })

  // Retornar interface simplificada com computeds locais
  return {
    // Estados computados locais para garantir reatividade
    isCollapsed: computed(() => uiStore.sidebarCollapsed),
    showText: computed(() => uiStore.sidebarTextVisible),
    isMobileOpen: computed(() => uiStore.mobileSidebarOpen),
    
    // Actions principais
    toggle: uiStore.toggleSidebar,
    collapse: uiStore.collapseSidebar,
    expand: uiStore.expandSidebar,
    
    // Actions mobile
    toggleMobile: uiStore.toggleMobileSidebar,
    closeMobile: uiStore.closeMobileSidebar,
    handleMobileLinkClick: uiStore.handleMobileLinkClick
  }
}