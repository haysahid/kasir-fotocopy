import { defineStore } from "pinia"
import { ref, computed, inject } from "vue"

export const useUserStore = defineStore('user', () => {
    const axios = inject('axios')

    const user = ref(false)

    async function fetchUser() {
        try {
            const { data } = await axios.get("/V1/api/v1/profile", {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem("access_token")}`
                }
            })
            user.value = data.data

            return true
        } catch (error) {
            console.error(error)
            user.value = false

            return false
        }
    }

    const isLoggedIn = computed(() => user.value !== false);

    function clearUser() {
        user.value = false
    }

    return { user, fetchUser, isLoggedIn, clearUser }
})