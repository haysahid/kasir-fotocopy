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
        category_id: null,
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
            let formData = new FormData();

            for (const key in item) {
                if (key === "image") {
                    formData.append("product_images[0]", item[key]);
                    continue;
                }

                formData.append(key, item[key]);
            }

            const response = await axios.post("/api/product", formData, {
                headers: { Authorization: token },
            });

            Toast.fire({
                icon: "success",
                title: response.data.meta.message,
            });

            saveStatus.value = "success";

            return response;
        } catch (error) {
            let errorText = "Terjadi kesalahan";

            if (error.response?.status === 422) {
                errorText = error.response?.data?.message || "Terjadi kesalahan";
            } else {
                errorText = error.response?.data?.meta?.message || "Terjadi kesalahan";
            }

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
            let formData = new FormData();

            for (const key in item) {
                if (key === "image") {
                    // Check image type is string or File
                    if (typeof item[key] === "string") {
                        continue;
                    }

                    formData.append("product_images[0]", item[key]);
                    continue;
                }

                if (item[key] === null) {
                    continue;
                }

                formData.append(key, item[key]);
            }

            formData.append("_method", "PUT");

            const response = await axios.post(`/api/product/${id}`, formData, {
                headers: { Authorization: token },
            });

            Toast.fire({
                icon: "success",
                title: response.data.meta.message,
            });

            saveStatus.value = "success";

            return response;
        } catch (error) {
            let errorText = "Terjadi kesalahan";

            if (error.response?.status === 422) {
                errorText = error.response?.data?.message || "Terjadi kesalahan";
            } else {
                errorText = error.response?.data?.meta?.message || "Terjadi kesalahan";
            }

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