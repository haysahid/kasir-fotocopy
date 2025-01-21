import { defineStore } from "pinia"
import { ref, computed, inject } from "vue"

export const useStoreConfigStore = defineStore('store-config', () => {
    const axios = inject("axios");
    const Toast = inject("Toast");
    const token = `Bearer ${localStorage.getItem("access_token")}`;

    const data = ref({});

    const getConfigStatus = ref("");
    const saveStatus = ref("");

    const deleteStatus = ref("");

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

    async function saveItem(items) {
        saveStatus.value = "loading";

        try {
            for (const index in items) {
                const data = items[index];

                const response = await axios.post("/api/store-config",
                    data,
                    {
                        headers: { Authorization: token },
                    });
            }

            Toast.fire({
                icon: "success",
                title: "Data berhasil disimpan",
            });

            saveStatus.value = "success";

            return true;
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

    async function deleteItem(item) {
        try {
            deleteStatus.value = "loading";

            const response = await axios.delete(
                `/api/store-config/${item.key}`,
                {
                    headers: {
                        Accept: "application/json",
                        Authorization: token,
                    },
                }
            );

            Toast.fire({
                icon: "success",
                title: "Item berhasil dihapus",
            });

            deleteStatus.value = "success";

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
        deleteItem,
        getConfigStatus,
        saveStatus,
        deleteStatus,
        errorMessage,
    };
})