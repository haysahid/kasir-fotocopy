import { ref } from 'vue'
import { defineStore } from 'pinia'
import { useStorage } from '@vueuse/core'

export const useDarkModeStore = defineStore('darkMode', () => {
  const darkMode = useStorage('darkMode', ref(false))

  document.documentElement.classList.toggle('dark', darkMode.value)

  function toggleDarkMode() {
    darkMode.value = !darkMode.value
    document.documentElement.classList.toggle('dark', darkMode.value)
  }

  function setDarkMode(value) {
    darkMode.value = value
    document.documentElement.classList.toggle('dark', value)
  }

  return { darkMode, toggleDarkMode, setDarkMode }
})
