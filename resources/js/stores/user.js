import { defineStore } from "pinia"
import { ref, computed, inject } from "vue"

export const useUserStore = defineStore('user', () => {
    const user = ref(false)

    const axios = inject("axios");

    async function fetchUser() {
        try {
            const { data } = await axios.get("/api/profile", {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem("access_token")}`
                }
            })
            user.value = data.result.user

            console.log(user.value)

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