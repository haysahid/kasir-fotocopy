import { defineStore } from "pinia"
import { ref, computed, inject } from "vue"

export const useRoleStore = defineStore('role', () => {
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

            const response = await axios.get("/api/role", {
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

    async function getDropdown(search) {
        try {
            getDropdownStatus.value = "loading";

            const response = await axios.get("/api/role-dropdown", {
                headers: { Authorization: token },
                params: { search },
            });

            dropdown.value = response.data.result;

            getDropdownStatus.value = "success";

            return response.data;
        } catch (error) {
            getDropdownStatus.value = "error";
            return {};
        }
    }

    function clearRoleStore() {
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
        getDropdown,
        clearRoleStore,
    }
})