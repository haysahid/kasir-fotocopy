import { get } from "@vueuse/core";
import { defineStore } from "pinia"
import { ref, computed, inject } from "vue"

export const useDashboardStore = defineStore('admin-dashboard', () => {
    const axios = inject("axios");
    const Toast = inject("Toast");
    const token = `Bearer ${localStorage.getItem("access_token")}`;


    // Get summary states
    const getSummaryStatus = ref({});

    // Get graph states
    const getGraphStatus = ref({});

    // Error message state
    const errorMessage = ref("");

    async function getSummary(storeId) {
        try {
            getSummaryStatus.value = "loading";

            const response = await axios.get(`/api/admin/summary`, {
                headers: { Authorization: token },
            });

            getSummaryStatus.value = "success";

            return response.data;
        } catch (error) {
            getSummaryStatus.value = "error";

            return {};
        }
    }

    function clearDashboardStore() {
        getSummaryStatus.value = {};
        getGraphStatus.value = {};
        errorMessage.value = "";
    }

    return {
        getSummaryStatus,
        getGraphStatus,
        errorMessage,
        getSummary,
        clearDashboardStore,
    }
})