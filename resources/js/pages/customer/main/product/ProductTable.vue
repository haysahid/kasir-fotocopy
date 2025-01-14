<script setup>
import { ref, onMounted, computed, watch } from "vue";
import ItemActionButton from "@/components/ItemActionButton.vue";
import CustomDialog from "@/components/Dialogs/CustomDialog.vue";
import DeleteProductConfirmation from "./DeleteProductConfirmation.vue";
import ProductForm from "./ProductForm.vue";
import CheckboxGroup from "@/components/Forms/CheckboxGroup.vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import { useProductStore } from "@/stores/product";

const props = defineProps({
    title: {
        type: String,
    },
});

const productStore = useProductStore();

const selectedItems = ref([]);
const isAllItemsSelected = ref(false);

const itemFormDialog = ref(null);
const deleteItemDialog = ref(null);

function showItemFormDialog(item) {
    if (item) {
        selectedItems.value = [item];
    }

    itemFormDialog.value.showModal();
}

function showDeleteItemDialog(item) {
    deleteItemDialog.value.showModal();
    if (item) {
        selectedItems.value = [item];
    }
}

function onItemFormDialogClosed(value) {
    itemFormDialog.value.close(value);

    if (value) {
        productStore.query.page = productStore.data.current_page;
        getData();
    }

    selectedItems.value = [];
}

function onDeleteItemDialogClosed(value) {
    deleteItemDialog.value.close(value);

    if (selectedItems.value.length == 1 && value == false) {
        selectedItems.value = [];
    }

    if (value) {
        selectedItems.value = [];
        productStore.query.page = productStore.data.current_page;
        getData();
    }
}

async function getData(params) {
    await productStore.getAllItems(params);
}

const changePage = (page) => {
    productStore.query.page = page;

    getData();

    document
        .getElementById("pagetop")
        ?.scrollIntoView({ block: "start", behavior: "smooth" });
};

watch(
    () => selectedItems.value.length,
    (newValue, oldValue) => {
        console.log("current page: ", productStore.data.current_page);
    }
);

watch(
    () => isAllItemsSelected.value,
    (newValue, oldValue) => {
        if (!oldValue && newValue) {
            selectedItems.value = JSON.parse(
                JSON.stringify(productStore.items)
            );
        }

        if (
            oldValue &&
            !newValue &&
            selectedItems.value.length === productStore.items?.length
        ) {
            selectedItems.value = [];
        }

        selectedItems.value = selectedItems.value;
    }
);

const selectionMode = computed(() => selectedItems.value?.length > 0);

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
    selectedItems,
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
            class="mb-6 text-xl font-semibold text-gray-900 dark:text-gray-200"
        >
            {{ props.title }}
        </h4>

        <!-- Table -->
        <div class="flex flex-col overflow-x-auto">
            <table class="table-fixed">
                <thead>
                    <tr
                        class="rounded-sm text-gray-500 bg-gray-100 dark:bg-gray-700 [&>th]:py-2.5 [&>th]:px-4 [&>th]:text-left duration-300 ease-linear"
                    >
                        <th class="w-0">
                            <CheckboxGroup :for="'formCheckbox'">
                                <input
                                    type="checkbox"
                                    :id="'formCheckbox'"
                                    class="sr-only taskCheckbox"
                                    :value="isAllItemsSelected"
                                    v-model="isAllItemsSelected"
                                />
                            </CheckboxGroup>
                        </th>

                        <th>
                            <h5
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Nama
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

                        <th class="w-0 max-sm:text-end">
                            <h5
                                v-if="!selectionMode"
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Aksi
                            </h5>
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="(item, key) in productStore.items"
                        :key="key"
                        class="hover:bg-secondary hover:bg-opacity-10 dark:hover:bg-opacity-5 [&>td]:py-2.5 [&>td]:px-4 [&>td]:text-sm duration-300 ease-linear"
                        :class="{
                            'border-b border-stroke dark:border-strokedark':
                                key !== productStore.items.length - 1,
                            'bg-secondary bg-opacity-20 dark:bg-opacity-10':
                                selectedItems
                                    .map((i) => i.id)
                                    .includes(item.id),
                        }"
                    >
                        <!-- Checkbox -->
                        <td class="py-2.5 px-4 w-0">
                            <CheckboxGroup :for="'formCheckbox_' + item.id">
                                <input
                                    type="checkbox"
                                    :id="'formCheckbox_' + item.id"
                                    class="sr-only taskCheckbox"
                                    :value="item"
                                    v-model="selectedItems"
                                />
                            </CheckboxGroup>
                        </td>

                        <!-- Name -->
                        <td>
                            <div class="flex items-center gap-3">
                                <img
                                    v-if="item.product_images.length > 0"
                                    :src="
                                        $getImageUrl(
                                            item.product_images[0].image
                                        )
                                    "
                                    alt="product image"
                                    class="object-cover w-10 h-10 rounded-lg"
                                />

                                <div
                                    v-else
                                    class="flex items-center justify-center w-10 h-10 duration-300 ease-linear bg-gray-100 rounded-lg dark:bg-slate-700"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="text-gray-400 dark:text-gray-500 size-6"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"
                                        />
                                    </svg>
                                </div>

                                <div>
                                    <p class="text-gray-900 dark:text-gray-200">
                                        {{ item.name }}
                                    </p>
                                    <p
                                        v-if="item.code"
                                        class="mt-0.5 text-xs text-gray-400 dark:text-gray-400"
                                    >
                                        {{ item.code }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <!-- Category -->
                        <td>
                            <p class="text-gray-900 dark:text-gray-200">
                                {{ item.category }}
                            </p>
                        </td>

                        <!-- Purchase Price -->
                        <td>
                            <p class="text-gray-900 dark:text-gray-200">
                                Rp {{ $formatCurrency(item.purchase_price) }}
                            </p>
                        </td>

                        <!-- Selling Price -->
                        <td>
                            <p class="text-gray-900 dark:text-gray-200">
                                Rp {{ $formatCurrency(item.selling_price) }}
                            </p>
                        </td>

                        <!-- Stock -->
                        <td>
                            <p class="text-gray-900 dark:text-gray-200">
                                {{ item.stock }}
                            </p>
                        </td>

                        <!-- Actions -->
                        <td class="py-2.5 px-4">
                            <ItemActionButton
                                v-if="!selectionMode"
                                @update-item="showItemFormDialog(item)"
                                @delete-item="showDeleteItemDialog(item)"
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            class="flex flex-col items-start justify-between gap-2 py-6 sm:items-center sm:flex-row"
        >
            <vue-awesome-paginate
                v-if="productStore?.data?.current_page"
                :total-items="productStore.data.total"
                :items-per-page="productStore.data.per_page"
                :max-pages-shown="5"
                v-model="productStore.data.current_page"
                @click="changePage"
            />
            <p class="text-xs font-light text-gray-400">
                Showing {{ productStore.data?.from }} to
                {{ productStore.data?.to }} of
                {{ productStore.data?.total }} entries
            </p>
        </div>

        <CustomDialog
            id="itemFormDialog"
            :show-cancel="true"
            @cancel="onItemFormDialogClosed"
        >
            <ProductForm
                :show-close-button="true"
                :item="selectedItems[0]"
                @close="onItemFormDialogClosed"
                class="max-sm:w-full sm:min-w-[400px] max-w-[400px]"
            />
        </CustomDialog>

        <CustomDialog id="deleteItemDialog" :show-cancel="true">
            <DeleteProductConfirmation
                :show-close-button="true"
                :items="selectedItems"
                @close="onDeleteItemDialogClosed"
                class="max-sm:w-full sm:min-w-[300px] max-w-[300px]"
            />
        </CustomDialog>
    </DefaultCard>
</template>
