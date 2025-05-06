import { defineStore } from "pinia"
import { ref, computed, inject } from "vue"

export const useCategoryStore = defineStore('category', () => {
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

    // Get dropdown states
    const dropdown = ref([]);
    const getDropdownStatus = ref("");

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

            const response = await axios.get("/api/category", {
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

    async function addItem(item) {
        saveStatus.value = "loading";

        try {
            let formData = new FormData();

            for (const key in item) {
                if (key === "image") {
                    formData.append("image", item[key]);
                    continue;
                }

                formData.append(key, item[key]);
            }

            const response = await axios.post("/api/category", formData, {
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

                    formData.append("image", item[key]);
                    continue;
                }

                if (item[key] === null) {
                    continue;
                }

                formData.append(key, item[key]);
            }

            formData.append("_method", "PUT");

            const response = await axios.post(`/api/category/${id}`, formData, {
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
                const response = await axios.delete(
                    `/api/category/${items[i].id}`,
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

    async function getDropdown(search, limit = 10) {
        try {
            getDropdownStatus.value = "loading";

            const response = await axios.get("/api/category-dropdown", {
                headers: { Authorization: token },
                params: { search, limit },
            });

            dropdown.value = response.data.result;

            getDropdownStatus.value = "success";

            return response.data;
        } catch (error) {
            getDropdownStatus.value = "error";
            return {};
        }
    }

    function clearStore() {
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
        item,
        dropdown,
        items,
        query,
        getAllItemsStatus,
        getDropdownStatus,
        saveStatus,
        deleteStatus,
        deleteProgress,
        errorMessage,
        getAllItems,
        addItem,
        updateItem,
        deleteItems,
        getDropdown,
        clearStore,
    }
})