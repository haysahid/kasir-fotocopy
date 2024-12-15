<script setup>
import { ref, onMounted, computed, watch } from "vue";
import ItemActionButton from "@/components/ItemActionButton.vue";
import CustomDialog from "@/components/Dialogs/CustomDialog.vue";
// import DeleteProductConfirmation from "./DeleteProductConfirmation.vue";
// import ProductForm from "./ProductForm.vue";
import CheckboxGroup from "@/components/Forms/CheckboxGroup.vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import { usePurchaseStore } from "@/stores/purchase";
import PurchaseForm from "../purchase/PurchaseForm.vue";

const props = defineProps({
    title: {
        type: String,
    },
});

const purchaseStore = usePurchaseStore();

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
        purchaseStore.query.page = purchaseStore.data.current_page;
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
        purchaseStore.query.page = purchaseStore.data.current_page;
        getData();
    }
}

async function getData(params) {
    await purchaseStore.getAllItems(params);
}

const changePage = (page) => {
    purchaseStore.query.page = page;

    getData();

    document
        .getElementById("pagetop")
        ?.scrollIntoView({ block: "start", behavior: "smooth" });
};

watch(
    () => selectedItems.value.length,
    (newValue, oldValue) => {
        console.log("current page: ", purchaseStore.data.current_page);
    }
);

watch(
    () => isAllItemsSelected.value,
    (newValue, oldValue) => {
        if (!oldValue && newValue) {
            selectedItems.value = JSON.parse(
                JSON.stringify(purchaseStore.items)
            );
        }

        if (
            oldValue &&
            !newValue &&
            selectedItems.value.length === purchaseStore.items?.length
        ) {
            selectedItems.value = [];
        }

        selectedItems.value = selectedItems.value;
    }
);

const selectionMode = computed(() => selectedItems.value?.length > 0);

const getPaymentStatus = (total, payment) => {
    if (total <= payment) {
        return "Lunas";
    }

    return "Belum Lunas";
};

onMounted(() => {
    getData();
    itemFormDialog.value = document.querySelector("#itemFormDialog");
    // deleteItemDialog.value = document.querySelector("#deleteItemDialog");
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
                                    :value="isAllItemsSelected"
                                    v-model="isAllItemsSelected"
                                />
                            </CheckboxGroup>
                        </th>

                        <th>
                            <h5
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Tanggal
                            </h5>
                        </th>

                        <th>
                            <h5
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Catatan
                            </h5>
                        </th>

                        <th>
                            <h5
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Jumlah Item
                            </h5>
                        </th>

                        <th>
                            <h5
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Total
                            </h5>
                        </th>

                        <th>
                            <h5
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Pembayaran
                            </h5>
                        </th>

                        <th>
                            <h5
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Kembalian
                            </h5>
                        </th>

                        <th>
                            <h5
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Status
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
                        v-for="(item, key) in purchaseStore.items"
                        :key="key"
                        class="hover:bg-secondary hover:bg-opacity-10 dark:hover:bg-opacity-5 [&>td]:py-2.5 [&>td]:px-4 [&>td]:text-sm duration-300 ease-linear"
                        :class="{
                            'border-b border-stroke dark:border-strokedark':
                                key !== purchaseStore.items.length - 1,
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

                        <!-- Created At -->
                        <td>
                            <p class="text-black dark:text-white">
                                {{
                                    $formatDate.formatDate(item.created_at, {
                                        dateStyle: "medium",
                                    })
                                }}
                            </p>
                            <p
                                v-if="item.code"
                                class="mt-0.5 text-xs text-gray-400 dark:text-gray-400"
                            >
                                {{ item.code }}
                            </p>
                        </td>

                        <!-- Note -->
                        <td>
                            <p class="text-black dark:text-white">
                                {{ item.note ?? "-" }}
                            </p>
                        </td>

                        <!-- Count Items -->
                        <td>
                            <p class="text-black dark:text-white">
                                {{ item.purchase_items.length }}
                            </p>
                        </td>

                        <!-- Selling Price -->
                        <td>
                            <p class="text-black dark:text-white">
                                Rp {{ $formatCurrency(item.total) }}
                            </p>
                        </td>

                        <!-- Payment -->
                        <td>
                            <p class="text-black dark:text-white">
                                Rp {{ $formatCurrency(item.payment) }}
                            </p>
                        </td>

                        <!-- Return -->
                        <td>
                            <p class="text-black dark:text-white">
                                Rp {{ $formatCurrency(item.return) }}
                            </p>
                        </td>

                        <!-- Payment Status -->
                        <td>
                            <p class="text-black dark:text-white">
                                {{ getPaymentStatus(item.total, item.payment) }}
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
                v-if="purchaseStore?.data?.current_page"
                :total-items="purchaseStore.data.total"
                :items-per-page="purchaseStore.data.per_page"
                :max-pages-shown="5"
                v-model="purchaseStore.data.current_page"
                @click="changePage"
            />
            <p class="text-xs font-light text-gray-400">
                Showing {{ purchaseStore.data?.from }} to
                {{ purchaseStore.data?.to }} of
                {{ purchaseStore.data?.total }} entries
            </p>
        </div>

        <CustomDialog
            id="itemFormDialog"
            :show-cancel="true"
            @cancel="onItemFormDialogClosed"
        >
            <PurchaseForm
                :show-close-button="true"
                :item="selectedItems[0]"
                @close="onItemFormDialogClosed"
                class="max-sm:w-full sm:min-w-[400px] max-w-[400px]"
            />
        </CustomDialog>

        <!-- <CustomDialog id="deleteItemDialog" :show-cancel="true">
            <DeleteProductConfirmation
                :show-close-button="true"
                :items="selectedItems"
                @close="onDeleteItemDialogClosed"
                class="max-sm:w-full sm:min-w-[300px] max-w-[300px]"
            />
        </CustomDialog> -->
    </DefaultCard>
</template>
