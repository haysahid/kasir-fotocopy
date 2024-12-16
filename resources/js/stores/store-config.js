import { defineStore } from "pinia"
import { ref, computed, inject } from "vue"

export const useStoreConfigStore = defineStore('store-config', () => {
    const axios = inject("axios");
    const Toast = inject("Toast");
    const token = `Bearer ${localStorage.getItem("access_token")}`;

    const data = ref({});

    const getConfigStatus = ref("");
    const saveStatus = ref("");

    const errorMessage = ref("");

    async function getConfig() {
        try {
            getConfigStatus.value = "loading";

            const response = await axios.get(`/api/store-config`, {
                headers: { Authorization: token }
            });

            data.value = response.data.result;

            getConfigStatus.value = "success";

            return response.data;
        } catch (error) {
            getConfigStatus.value = "error";

            return {};
        }
    }

    async function saveItem(item) {
        saveStatus.value = "loading";

        try {
            for (const key in item) {
                const value = item[key];

                const response = await axios.post("/api/store-config",
                    {
                        key: key,
                        value: value,
                    },
                    {
                        headers: { Authorization: token },
                    });
            }

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



    async function deleteItems(items) {
        try {
            deleteStatus.value = "loading";

            for (let i = 0; i < items.length; i++) {
                const response = await axios.delete(
                    `/api/sales/${items[i].id}`,
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

    return {
        data,
        getConfig,
        saveItem,
        deleteItems,
        getConfigStatus,
        saveStatus,
        errorMessage,
    };
})