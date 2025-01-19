import { defineStore } from "pinia"
import { ref, computed, inject } from "vue"
import { useStorage } from '@vueuse/core'


export const useUserStore = defineStore('user', () => {
    const user = ref(localStorage.getItem("user") ? JSON.parse(localStorage.getItem("user")) : false)

    const axios = inject("axios");
    const Toast = inject("Toast");
    const token = `Bearer ${localStorage.getItem("access_token")}`;

    const subscriptionHistoryData = ref(null);
    const getSubscriptionHistoryStatus = ref("");

    const query = ref({
        limit: 10,
        page: 1,
        search: null,
    });

    async function fetchUser() {
        if (!localStorage.getItem("access_token")) {
            user.value = false
            return false
        }

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
            const errorText = error.response?.data?.meta?.message || "Terjadi kesalahan";

            if (error.response?.data?.meta?.code === 401) {
                Toast.fire({
                    icon: "warning",
                    title: errorText,
                });

                localStorage.removeItem("access_token");
            }

            user.value = false

            return false
        }
    }

    async function getSubscriptionHistory(params) {
        if (params) query.value = params;

        try {
            getSubscriptionHistoryStatus.value = "loading";

            const response = await axios.get("/api/subscription-history", {
                headers: { Authorization: token },
                params: query.value,
            })

            subscriptionHistoryData.value = response.data.result;

            getSubscriptionHistoryStatus.value = "success";

            return response.data;
        } catch (error) {
            getSubscriptionHistoryStatus.value = "error";

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
        hasActiveSubscription,
        subscriptionHistoryData,
        getSubscriptionHistory,
        getSubscriptionHistoryStatus,
        query,
    }
})