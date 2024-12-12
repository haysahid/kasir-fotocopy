import { defineStore } from "pinia"
import { ref, computed, inject } from "vue"


export const useProductStore = defineStore('product', () => {
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

    // Error message state
    const errorMessage = ref("");

    async function getAllItems(params) {
        if (params) query.value = params;

        try {
            getAllItemsStatus.value = "loading";

            const response = await axios.get("/api/product", {
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

            const response = await axios.get(`/api/product/${id}`, {
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
            const response = await axios.post("/api/product", item, {
                headers: { Authorization: token },
            });

            Toast.fire({
                icon: "success",
                title: response.data.meta.message,
            });

            saveStatus.value = "success";

            return response.data;
        } catch (error) {
            Toast.fire({
                icon: "warning",
                title: error.response?.data?.message || "Terjadi kesalahan" + error,
            });

            saveStatus.value = "error";
            errorMessage.value = "Terjadi kesalahan";

            return {};
        }
    }

    async function updateItem(item) {
        saveStatus.value = "loading";

        try {
            const response = await axios.put("/api/product", item, {
                headers: { Authorization: token },
            });

            Toast.fire({
                icon: "success",
                title: response.data.meta.message,
            });

            saveStatus.value = "success";

            return response.data;
        } catch (error) {
            Toast.fire({
                icon: "warning",
                title: error.response?.data?.message || "Terjadi kesalahan" + error,
            });

            saveStatus.value = "error";
            errorMessage.value = "Terjadi kesalahan";

            return {};
        }
    }

    async function deleteItems(items) {
        try {
            deleteStatus.value = "loading";

            for (let i = 0; i < items.length; i++) {
                const response = await axios.put(
                    `/api/product/${items[i].id}/disable`,
                    {},
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

            return response.data;
        } catch (error) {
            Toast.fire({
                icon: "warning",
                title: error.response?.data?.message || "Terjadi kesalahan",
            });

            deleteStatus.value = "error";
        }
    }

    function clearProductStore() {
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
        errorMessage,
        getAllItems,
        getItem,
        addItem,
        updateItem,
        deleteItems,
        clearProductStore,
    }
})