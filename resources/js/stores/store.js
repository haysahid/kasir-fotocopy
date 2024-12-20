import { get } from "@vueuse/core";
import { defineStore } from "pinia"
import { ref, computed, inject } from "vue"

export const useStoreStore = defineStore('store', () => {
    const axios = inject("axios");
    const Toast = inject("Toast");
    const token = `Bearer ${localStorage.getItem("access_token")}`;

    // Get all items states
    const data = ref({});
    const query = ref({
        limit: 10,
        page: 1,
        search: null,
    });
    const getAllItemsStatus = ref("");
    const items = computed(() => data.value.data);

    // Get item states
    const item = ref({});
    const getItemStatus = ref("");

    // Save item states
    const saveStatus = ref("");

    // Delete item states
    const deleteStatus = ref("");
    const deleteProgress = ref(0);

    // Get summary states
    const getSummaryStatus = ref({});

    // Get graph states
    const getGraphStatus = ref({});

    // Error message state
    const errorMessage = ref("");

    async function getAllItems(params) {
        if (params) query.value = params;

        try {
            getAllItemsStatus.value = "loading";

            const response = await axios.get("/api/store", {
                headers: { Authorization: token },
                params: query.value,
            });

            data.value = response.data.result;

            getAllItemsStatus.value = "success";

            return response.data;
        } catch (error) {
            getAllItemsStatus.value = "error";

            return {};
        }
    }

    async function getItem(id) {
        if (params) query.value = params;

        try {
            getItemStatus.value = "loading";

            const response = await axios.get(`/api/store/${id}`, {
                headers: { Authorization: token }
            });

            data.value = response.data.result;

            getItemStatus.value = "success";

            return response.data;
        } catch (error) {
            getItemStatus.value = "error";

            return {};
        }
    }

    async function addItem(item) {
        saveStatus.value = "loading";

        try {
            const response = await axios.post("/api/store", item, {
                headers: { Authorization: token },
            });

            Toast.fire({
                icon: "success",
                title: response.data.meta.message,
            });

            saveStatus.value = "success";

            return response.data;
        } catch (error) {
            const errorText = error.response?.data?.meta?.message || "Terjadi kesalahan";

            Toast.fire({
                icon: "warning",
                title: errorText,
            });

            saveStatus.value = "error";
            errorMessage.value = errorText;

            return error.response?.data;
        }
    }

    async function updateItem(id, item) {
        saveStatus.value = "loading";

        try {
            const response = await axios.put(`/api/store/${id}`, item, {
                headers: { Authorization: token },
            });

            Toast.fire({
                icon: "success",
                title: response.data.meta.message,
            });

            saveStatus.value = "success";

            return response.data;
        } catch (error) {
            const errorText = error.response?.data?.meta?.message || "Terjadi kesalahan";

            Toast.fire({
                icon: "warning",
                title: errorText,
            });

            saveStatus.value = "error";
            errorMessage.value = errorText;

            return {};
        }
    }

    async function deleteItems(items) {
        try {
            deleteStatus.value = "loading";

            for (let i = 0; i < items.length; i++) {
                const response = await axios.delete(
                    `/api/store/${items[i].id}`,
                    {
                        headers: {
                            Accept: "application/json",
                            Authorization: token,
                        },
                    }
                );

                deleteProgress.value++;
            }

            Toast.fire({
                icon: "success",
                title: "Item berhasil dihapus",
            });

            deleteStatus.value = "success";
            deleteProgress.value = 0;

            return true;
        } catch (error) {
            const errorText = error.response?.data?.meta?.message || "Terjadi kesalahan";

            Toast.fire({
                icon: "warning",
                title: errorText,
            });

            deleteStatus.value = "error";

            return false;
        }
    }

    async function getSummary(storeId) {
        try {
            getSummaryStatus.value = "loading";

            const response = await axios.get(`/api/store/${storeId}/summary`, {
                headers: { Authorization: token },
            });

            getSummaryStatus.value = "success";

            return response.data;
        } catch (error) {
            getSummaryStatus.value = "error";

            return {};
        }
    }

    async function getGraph(storeId, params) {
        params = {
            startDate: null,
            endDate: null,
            type: 'daily',
            year: null,
            month: null,
            week: null,
            ...params,
        }

        try {
            getGraphStatus.value = "loading";

            const response = await axios.get(`/api/store/${storeId}/graph`, {
                headers: { Authorization: token },
                params: params,
            });

            getGraphStatus.value = "success";

            return response.data;
        } catch (error) {
            getGraphStatus.value = "error";

            return {};
        }
    }

    async function disableOrEnableItem(item) {
        try {
            const action = item.is_active ? "enable" : "disable";

            const response = await axios.put(
                `/api/store/${item.id}/${action}`,
                {},
                {
                    headers: {
                        Accept: "application/json",
                        Authorization: token,
                    },
                }
            );

            Toast.fire({
                icon: "success",
                title: response.data.meta.message,
            });

            return true;
        } catch (error) {
            const errorText = error.response?.data?.meta?.message || "Terjadi kesalahan";

            Toast.fire({
                icon: "warning",
                title: errorText,
            });

            return false;
        }
    }

    function clearStoreStore() {
        data.value = {}
        query.value = {
            limit: 10,
            page: 1,
            search: null,
        }
        getAllItemsStatus.value = ""
        item.value = {}
        getItemStatus.value = ""
        saveStatus.value = ""
        deleteStatus.value = ""
        deleteProgress.value = 0
        errorMessage.value = ""
    }

    return {
        data,
        items,
        query,
        getAllItemsStatus,
        saveStatus,
        deleteStatus,
        deleteProgress,
        getSummaryStatus,
        getGraphStatus,
        errorMessage,
        getAllItems,
        getItem,
        addItem,
        updateItem,
        deleteItems,
        disableOrEnableItem,
        getSummary,
        getGraph,
        clearStoreStore,
    }
})