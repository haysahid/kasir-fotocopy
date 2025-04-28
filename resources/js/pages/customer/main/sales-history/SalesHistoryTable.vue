<script setup>
import { ref, onMounted, computed, watch } from "vue";
import ItemActionButton from "@/components/ItemActionButton.vue";
import CustomDialog from "@/components/Dialogs/CustomDialog.vue";
import CheckboxGroup from "@/components/Forms/CheckboxGroup.vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import { useSalesStore } from "@/stores/sales";
import SalesForm from "../sales/SalesForm.vue";
import DeleteSalesConfirmation from "./DeleteSalesConfirmation.vue";
import PrintSalesReceipt from "../sales/PrintSalesReceipt.vue";

const props = defineProps({
    title: {
        type: String,
    },
});

const salesStore = useSalesStore();

const selectedItems = ref([]);
const isAllItemsSelected = ref(false);

const itemFormDialog = ref(null);
const deleteItemDialog = ref(null);
const printItemDialog = ref(null);

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

function showPrintItemDialog(item) {
    printItemDialog.value.showModal();
    if (item) {
        selectedItems.value = [item];
    }
}

function onItemFormDialogClosed(value) {
    itemFormDialog.value.close(value);

    if (value) {
        salesStore.query.page = salesStore.data.current_page;
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
        salesStore.query.page = salesStore.data.current_page;
        getData();
    }
}

function onPrintItemDialogClosed(value) {
    printItemDialog.value.close(value);
    selectedItems.value = [];
}

async function getData(params) {
    await salesStore.getAllItems(params);
}

const changePage = (page) => {
    salesStore.query.page = page;

    getData();

    document
        .getElementById("pagetop")
        ?.scrollIntoView({ block: "start", behavior: "smooth" });
};

watch(
    () => selectedItems.value.length,
    (newValue, oldValue) => {
        console.log("current page: ", salesStore.data.current_page);
    }
);

watch(
    () => isAllItemsSelected.value,
    (newValue, oldValue) => {
        if (!oldValue && newValue) {
            selectedItems.value = JSON.parse(JSON.stringify(salesStore.items));
        }

        if (
            oldValue &&
            !newValue &&
            selectedItems.value.length === salesStore.items?.length
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
    printItemDialog.value = document.querySelector("#printItemDialog");
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
                                Qty
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
                                Bayar
                            </h5>
                        </th>

                        <th>
                            <h5
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Kembali
                            </h5>
                        </th>

                        <th>
                            <h5
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Status
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
                        v-for="(item, key) in salesStore.items"
                        :key="key"
                        class="hover:bg-secondary hover:bg-opacity-10 dark:hover:bg-opacity-5 [&>td]:py-2.5 [&>td]:px-4 [&>td]:text-sm duration-300 ease-linear"
                        :class="{
                            'border-b border-stroke dark:border-strokedark':
                                key !== salesStore.items.length - 1,
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
                            <p class="text-gray-900 dark:text-gray-200">
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
                            <p class="text-gray-900 dark:text-gray-200">
                                {{ item.note ?? "-" }}
                            </p>
                        </td>

                        <!-- Count Items -->
                        <td>
                            <p class="text-gray-900 dark:text-gray-200">
                                {{ item.sales_items.length }}
                            </p>
                        </td>

                        <!-- Selling Price -->
                        <td>
                            <p class="text-gray-900 dark:text-gray-200">
                                Rp {{ $formatCurrency(item.total) }}
                            </p>
                        </td>

                        <!-- Payment -->
                        <td>
                            <p class="text-gray-900 dark:text-gray-200">
                                Rp {{ $formatCurrency(item.payment) }}
                            </p>
                        </td>

                        <!-- Return -->
                        <td>
                            <p class="text-gray-900 dark:text-gray-200">
                                Rp {{ $formatCurrency(item.return) }}
                            </p>
                        </td>

                        <!-- Payment Status -->
                        <td>
                            <p
                                class="text-gray-900 dark:text-gray-200"
                                :class="{
                                    'text-red-600 dark:text-red-600':
                                        item.status === 'Belum Lunas',
                                    'text-green-600 dark:text-green-600':
                                        item.status === 'Lunas',
                                }"
                            >
                                {{ item.status }}
                            </p>
                        </td>

                        <!-- Actions -->
                        <td class="py-2.5 px-4">
                            <ItemActionButton
                                v-if="!selectionMode"
                                @update-item="showItemFormDialog(item)"
                                @delete-item="showDeleteItemDialog(item)"
                                :showPrintButton="true"
                                @print-item="showPrintItemDialog(item)"
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
                v-if="salesStore?.data?.current_page"
                :total-items="salesStore.data.total"
                :items-per-page="salesStore.data.per_page"
                :max-pages-shown="5"
                v-model="salesStore.data.current_page"
                @click="changePage"
            />
            <p class="text-xs font-light text-gray-400">
                Showing {{ salesStore.data?.from }} to
                {{ salesStore.data?.to }} of
                {{ salesStore.data?.total }} entries
            </p>
        </div>

        <CustomDialog id="itemFormDialog" :show-cancel="true">
            <SalesForm
                :show-close-button="true"
                :item="selectedItems ? selectedItems[0] : null"
                @close="onItemFormDialogClosed"
                class="max-sm:w-full sm:min-w-[400px] max-w-[400px]"
            />
        </CustomDialog>

        <CustomDialog id="deleteItemDialog" :show-cancel="true">
            <DeleteSalesConfirmation
                :show-close-button="true"
                :items="selectedItems"
                @close="onDeleteItemDialogClosed"
                class="max-sm:w-full sm:min-w-[300px] max-w-[300px]"
            />
        </CustomDialog>

        <CustomDialog id="printItemDialog" :show-cancel="true">
            <PrintSalesReceipt
                :show-close-button="true"
                :sales="selectedItems[0] ?? null"
                @close="onPrintItemDialogClosed"
                class="w-full lg:w-[800px]"
            />
        </CustomDialog>
    </DefaultCard>
</template>
