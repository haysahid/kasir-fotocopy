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

const checkedItems = ref([]);
const isAllItemsChecked = ref(false);

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
    if (item) {
        checkedItems.value = [item];
    }
}

function onItemFormDialogClosed(value) {
    itemFormDialog.value.close(value);

    if (value) {
        productStore.query.page = productStore.data.current_page;
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
    () => checkedItems.value.length,
    (newValue, oldValue) => {
        console.log("current page: ", productStore.data.current_page);
    }
);

watch(
    () => isAllItemsChecked.value,
    (newValue, oldValue) => {
        if (!oldValue && newValue) {
            checkedItems.value = JSON.parse(JSON.stringify(productStore.items));
        }

        if (
            oldValue &&
            !newValue &&
            checkedItems.value.length === productStore.items?.length
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

                        <th v-show="!selectionMode" class="w-0 max-sm:text-end">
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
                        v-for="(item, key) in productStore.items"
                        :key="key"
                        class="hover:bg-secondary hover:bg-opacity-10 dark:hover:bg-opacity-5 [&>td]:py-2.5 [&>td]:px-4 [&>td]:text-sm duration-300 ease-linear"
                        :class="{
                            'border-b border-stroke dark:border-strokedark':
                                key !== productStore.items.length - 1,
                            'bg-secondary bg-opacity-20 dark:bg-opacity-10':
                                checkedItems.map((i) => i.id).includes(item.id),
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
                :item="checkedItems[0]"
                @close="onItemFormDialogClosed"
                class="max-sm:w-full sm:min-w-[400px] max-w-[400px]"
            />
        </CustomDialog>

        <CustomDialog id="deleteItemDialog" :show-cancel="true">
            <DeleteProductConfirmation
                :show-close-button="true"
                :items="checkedItems"
                @close="onDeleteItemDialogClosed"
                class="max-sm:w-full sm:min-w-[300px] max-w-[300px]"
            />
        </CustomDialog>
    </DefaultCard>
</template>
