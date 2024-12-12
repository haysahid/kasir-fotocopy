<script setup>
import { ref, inject, onMounted, computed, watch } from "vue";
import ItemActionButton from "@/components/ItemActionButton.vue";
import CustomDialog from "@/components/Dialogs/CustomDialog.vue";
import DeleteProductConfirmation from "./DeleteProductConfirmation.vue";
import { RouterLink } from "vue-router";
import StatusLabel from "@/components/StatusLabel.vue";
import ProductForm from "./ProductForm.vue";
import CheckboxGroup from "@/components/Forms/CheckboxGroup.vue";
import DefaultCard from "../../../../components/Forms/DefaultCard.vue";

const props = defineProps({
    title: {
        type: String,
    },
});

const axios = inject("axios");

const data = ref({});
const checkedItems = ref([]);
const isAllItemsChecked = ref(false);
const getDataState = ref("");

const query = ref({
    limit: null,
    page: 1,
    search: "",
});

const itemFormDialog = ref(null);
const deleteItemDialog = ref(null);

function showItemFormDialog(item) {
    if (item) {
        checkedItems.value = [item];
    }

    itemFormDialog.value.showModal();
}

function showDeleteItemDialog(item) {
    deleteItemDialog.value.showModal();
    checkedItems.value = [item];
}

function onItemFormDialogClosed(value) {
    itemFormDialog.value.close(value);

    if (value) {
        query.value.page = data.value.current_page;
        getData();
    }

    checkedItems.value = [];
}

function onDeleteItemDialogClosed(value) {
    deleteItemDialog.value.close(value);

    if (checkedItems.value.length == 1 && value == false) {
        checkedItems.value = [];
    }

    if (value) {
        checkedItems.value = [];
        query.value.page = data.value.page;
        getData();
    }
}

async function getData(params) {
    if (params) query.value = params;

    try {
        getDataState.value = "loading";

        const token = `Bearer ${localStorage.getItem("access_token")}`;
        const response = await axios.get("/api/product", {
            headers: { Authorization: token },
            params: query.value,
        });
        data.value = response.data.result;

        getDataState.value = "success";
    } catch (error) {
        getDataState.value = "error";
        console.error(error);
    }
}

const items = computed(() => data.value.data);

const changePage = (page) => {
    query.value.page = page;
    getData();

    document
        .getElementById("pagetop")
        ?.scrollIntoView({ block: "start", behavior: "smooth" });
};

watch(
    () => checkedItems.value.length,
    (newValue, oldValue) => {
        if (newValue === items.value?.length) {
            isAllItemsChecked.value = true;
        }

        if (newValue < items.value.length) {
            isAllItemsChecked.value = false;
        }
    }
);

watch(
    () => isAllItemsChecked.value,
    (newValue, oldValue) => {
        if (!oldValue && newValue) {
            checkedItems.value = JSON.parse(JSON.stringify(items.value));
        }

        if (
            oldValue &&
            !newValue &&
            checkedItems.value.length === items.value?.length
        ) {
            checkedItems.value = [];
        }

        checkedItems.value = checkedItems.value;
    }
);

const selectionMode = computed(() => checkedItems.value?.length > 0);

onMounted(() => {
    getData();
    itemFormDialog.value = document.querySelector("#itemFormDialog");
    deleteItemDialog.value = document.querySelector("#deleteItemDialog");
});

defineExpose({
    getData,
    showItemFormDialog,
    showDeleteItemDialog,
    selectionMode,
    checkedItems,
});
</script>

<template>
    <DefaultCard
        :showShadow="false"
        :showBorder="false"
        class="px-4 pt-6 pb-2.5 sm:px-6.5 xl:pb-1"
    >
        <h4
            v-if="props.title"
            class="mb-6 text-xl font-semibold text-black dark:text-white"
        >
            {{ props.title }}
        </h4>

        <!-- Table -->
        <div class="flex flex-col overflow-x-auto">
            <table class="table-auto">
                <thead>
                    <tr
                        class="rounded-sm text-gray-500 bg-gray-100 dark:bg-gray-700 [&>th]:py-2.5 [&>th]:px-4 [&>th]:text-left duration-300 ease-linear"
                    >
                        <th class="w-[50px]">
                            <CheckboxGroup :for="'formCheckbox'">
                                <input
                                    type="checkbox"
                                    :id="'formCheckbox'"
                                    class="sr-only taskCheckbox"
                                    :value="isAllItemsChecked"
                                    v-model="isAllItemsChecked"
                                />
                            </CheckboxGroup>
                        </th>

                        <th>
                            <h5
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Name
                            </h5>
                        </th>

                        <th>
                            <h5
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Kategori
                            </h5>
                        </th>

                        <th>
                            <h5
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Harga Beli
                            </h5>
                        </th>

                        <th>
                            <h5
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Harga Jual
                            </h5>
                        </th>

                        <th>
                            <h5
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Stok
                            </h5>
                        </th>

                        <th v-show="!selectionMode" class="max-sm:text-end">
                            <h5
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Aksi
                            </h5>
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="(item, key) in items"
                        :key="key"
                        class="hover:bg-secondary hover:bg-opacity-10 dark:hover:bg-opacity-5 [&>td]:py-2.5 [&>td]:px-4 [&>td]:text-sm duration-300 ease-linear"
                        :class="{
                            'border-b border-stroke dark:border-strokedark':
                                key !== items.length - 1,
                            'bg-secondary bg-opacity-20 dark:bg-opacity-10':
                                checkedItems.map((i) => i.id).includes(item.id),
                        }"
                    >
                        <!-- Checkbox -->
                        <td class="py-2.5 px-4 w-[50px]">
                            <CheckboxGroup :for="'formCheckbox_' + item.id">
                                <input
                                    type="checkbox"
                                    :id="'formCheckbox_' + item.id"
                                    class="sr-only taskCheckbox"
                                    :value="item"
                                    v-model="checkedItems"
                                />
                            </CheckboxGroup>
                        </td>

                        <!-- Name -->
                        <td>
                            <p class="text-black dark:text-white">
                                {{ item.name }}
                            </p>
                            <p
                                v-if="item.code"
                                class="text-black dark:text-white"
                            >
                                {{ item.code }}
                            </p>
                        </td>

                        <!-- Category -->
                        <td>
                            <p class="text-black dark:text-white">
                                {{ item.category }}
                            </p>
                        </td>

                        <!-- Purchase Price -->
                        <td>
                            <p class="text-black dark:text-white">
                                {{ $formatCurrency(item.purchase_price) }}
                            </p>
                        </td>

                        <!-- Selling Price -->
                        <td>
                            <p class="text-black dark:text-white">
                                {{ $formatCurrency(item.selling_price) }}
                            </p>
                        </td>

                        <!-- Stock -->
                        <td>
                            <p class="text-black dark:text-white">
                                {{ item.stock }}
                            </p>
                        </td>

                        <!-- Actions -->
                        <td v-show="!selectionMode" class="py-2.5 px-4">
                            <ItemActionButton
                                @update-item="showItemFormDialog(item)"
                                @delete-item="showDeleteItemDialog(item)"
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <vue-awesome-paginate
            v-if="data.current_page"
            :total-items="data.total"
            :items-per-page="data.per_page"
            :max-pages-shown="5"
            v-model="data.current_page"
            :on-click="changePage"
            class="py-6"
        />

        <CustomDialog
            id="itemFormDialog"
            :show-cancel="true"
            @cancel="onItemFormDialogClosed"
        >
            <ProductForm
                :show-close-button="true"
                :item="checkedItems[0]"
                @close="onItemFormDialogClosed"
                class="sm:min-w-[400px] max-w-[400px]"
            />
        </CustomDialog>

        <CustomDialog id="deleteItemDialog" :show-cancel="true">
            <DeleteProductConfirmation
                :show-close-button="true"
                :items="checkedItems"
                @close="onDeleteItemDialogClosed"
                class="max-w-[400px]"
            />
        </CustomDialog>
    </DefaultCard>
</template>
