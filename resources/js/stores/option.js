import { get } from "@vueuse/core";
import { defineStore } from "pinia"
import { ref, computed, inject } from "vue"

export const useOptionStore = defineStore('option', () => {
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

    // Get dropdown
    const dropdown = ref([]);

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

            const response = await axios.get("/api/option", {
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

            const response = await axios.get(`/api/option/${id}`, {
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
            const response = await axios.post("/api/option", item, {
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
            const response = await axios.put(`/api/option/${id}`, item, {
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
                    `/api/option/${items[i].id}`,
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

    async function disableOrEnableItem(item) {
        try {
            const action = item.is_active ? "enable" : "disable";

            const response = await axios.put(
                `/api/option/${item.id}/${action}`,
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

    async function getDropdown(search) {
        try {
            const response = await axios.get("/api/option-dropdown", {
                headers: { Authorization: token },
                params: { search },
            });

            dropdown.value = response.data.result;

            return response.data;
        } catch (error) {
            return {};
        }
    }

    function clearOptionStore() {
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
        dropdown,
        item,
        items,
        query,
        getAllItemsStatus,
        saveStatus,
        deleteStatus,
        deleteProgress,
        errorMessage,
        getAllItems,
        getDropdown,
        getItem,
        addItem,
        updateItem,
        deleteItems,
        disableOrEnableItem,
        clearOptionStore,
    }
})