import { useStorage } from '@vueuse/core'
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useSidebarStore = defineStore('sidebar', () => {
  const isSidebarOpen = ref(false)
  const selected = useStorage('selected', ref(''))
  const page = useStorage('page', ref('/'))

  function toggleSidebar() {
    isSidebarOpen.value = !isSidebarOpen.value
  }

  function initSidebar(initPage = '/', initSelected = '') {
    selected.value = initSelected
    page.value = initPage
  }

  function setSidebarByFullPath(path) {
    const firstPath = path ? path.split('/')[1] : ''
    page.value = `/${firstPath}`

    const secondPath = path.split('/')[2] || ''

    selected.value = secondPath ? `/${firstPath}/${secondPath}` : ''
  }

  return { isSidebarOpen, initSidebar, setSidebarByFullPath, toggleSidebar, selected, page }
})
