import { defineStore } from "pinia"
import { ref, computed, inject } from "vue"
import { useStorage } from '@vueuse/core'


export const useUserStore = defineStore('user', () => {
    const user = ref(localStorage.getItem("user") ? JSON.parse(localStorage.getItem("user")) : false)

    const axios = inject("axios");

    async function fetchUser() {
        try {
            const { data } = await axios.get("/api/profile", {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem("access_token")}`
                }
            })

            localStorage.setItem("user", JSON.stringify(data.result.user))

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

    const isEnabled = computed(() => {
        return user.value.disabled_at === null
    });

    const hasStore = computed(() => {
        return user.value.store?.length > 0
    });

    const hasActiveSubscription = computed(() => {
        return user.value.has_active_subscription
    });

    function clearUser() {
        user.value = false
    }

    return {
        user,
        fetchUser,
        isLoggedIn,
        clearUser,
        isEnabled,
        hasStore,
        hasActiveSubscription
    }
})