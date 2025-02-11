<script setup>
import { ref, onMounted, computed, watch } from "vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import { useProductStore } from "@/stores/product";
import CustomSearchBar from "@/components/Forms/CustomSearchBar.vue";
import ProductItem from "../product/ProductItem.vue";
import SalesCartItem from "./SalesCartItem.vue";
import PageSection from "@/components/Sections/PageSection.vue";
import EmptyCart from "./EmptyCart.vue";
import CustomButton from "@/components/Forms/CustomButton.vue";
import CustomDialog from "@/components/Dialogs/CustomDialog.vue";
import SalesForm from "./SalesForm.vue";
import PrintSalesReceipt from "./PrintSalesReceipt.vue";

const props = defineProps({
    title: {
        type: String,
    },
});

const productStore = useProductStore();

const selectedItems = ref([]);
const isAllItemsSelected = ref(false);

const sales = ref(null);

const query = ref({
    limit: null,
    page: 1,
    search: null,
});

const itemFormDialog = ref(null);
const printItemDialog = ref(null);

function showItemFormDialog() {
    itemFormDialog.value.showModal();
}

function onItemFormDialogClosed(value) {
    itemFormDialog.value.close(value);

    if (value) {
        selectedItems.value = [];
        isAllItemsSelected.value = false;
        getData();
    }
}

function showPrintItemDialog(item) {
    sales.value = item;
    printItemDialog.value.showModal();
}

function onPrintItemDialogClosed(value) {
    sales.value = null;
    printItemDialog.value.close(value);
}

function showSuccessDialog() {
    itemFormDialog.value.showModal();
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

function selectItem(item) {
    const cartItem = {
        id: item.id,
        product_id: item.id,
        product: item,
        quantity: 1,
        item_price: item.selling_price,
    };

    if (selectedItems.value.map((e) => e.id).includes(item.id)) {
        selectedItems.value = selectedItems.value.filter(
            (selectedItem) => selectedItem.id !== item.id
        );
    } else {
        selectedItems.value = [...selectedItems.value, cartItem];
    }
}

function updateItem(item) {
    selectedItems.value = selectedItems.value.map((selectedItem) => {
        if (selectedItem.id === item.id) {
            return item;
        }

        return selectedItem;
    });
}

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

watch(
    () => query.value.search,
    () => {
        getData(query.value);
    }
);

const selectionMode = computed(() => selectedItems.value?.length > 0);
const total = computed(() =>
    selectedItems.value.reduce(
        (acc, item) => acc + item.item_price * item.quantity,
        0
    )
);

onMounted(() => {
    getData();
    itemFormDialog.value = document.querySelector("#itemFormDialog");
    printItemDialog.value = document.querySelector("#printItemDialog");
});

defineExpose({
    getData,
    showItemFormDialog,
    selectionMode,
    checkedItems: selectedItems,
});
</script>

<template>
    <div class="flex flex-col gap-6 md:flex-row">
        <!-- Products -->
        <div class="w-full">
            <h4
                v-if="props.title"
                class="mb-6 text-xl font-semibold text-gray-900 dark:text-gray-200"
            >
                {{ props.title }}
            </h4>

            <div class="flex flex-col gap-4">
                <PageSection
                    :page-title="'Penjualan'"
                    id="pagetop"
                    class="col-span-12 text-sm xl:col-span-8"
                >
                    <div class="flex items-center justify-center w-full gap-4">
                        <CustomSearchBar
                            v-model="query.search"
                            id="search"
                            placeholder="Cari produk / jasa"
                            :useDebounce="true"
                        />
                    </div>
                </PageSection>

                <!-- List -->
                <div
                    class="grid grid-flow-row grid-cols-1 gap-2 xl:grid-cols-2"
                >
                    <DefaultCard
                        v-for="product in productStore.items"
                        @click="selectItem(product)"
                        :key="product.id"
                        :showShadow="false"
                        :showBorder="false"
                        class="p-4 cursor-pointer hover:bg-secondary hover:bg-opacity-30 dark:hover:bg-slate-700"
                        :class="{
                            '!bg-secondary !bg-opacity-50 dark:!bg-slate-700':
                                selectedItems
                                    .map((e) => e.id)
                                    .includes(product.id),
                        }"
                    >
                        <ProductItem
                            :product="product"
                            priceToShow="selling_price"
                            :isSelected="
                                selectedItems
                                    .map((e) => e.id)
                                    .includes(product.id)
                            "
                            :inCartQuantity="
                                parseInt(
                                    selectedItems.find(
                                        (e) => e.id === product.id
                                    )?.quantity
                                ) || 0
                            "
                        />
                    </DefaultCard>
                </div>

                <!-- Pagination -->
                <div
                    class="flex flex-col items-start justify-between gap-2 py-2 sm:items-center sm:flex-row"
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
            </div>
        </div>

        <!-- Cart -->
        <div class="md:w-3/5 xl:w-2/5 min-h-[50%]">
            <DefaultCard
                class="flex flex-col justify-between w-full"
                cardTitle="Keranjang Penjualan"
                :showShadow="false"
                :showBorder="false"
            >
                <div
                    v-if="selectedItems.length > 0"
                    class="h-full duration-300 ease-linear"
                >
                    <SalesCartItem
                        v-for="cartItem in selectedItems"
                        :key="cartItem.id"
                        :id="'cart-item-' + cartItem.id"
                        :cartItem="cartItem"
                        @removeItem="selectItem"
                        @updateItem="updateItem"
                    />
                </div>

                <EmptyCart v-else />

                <div class="flex flex-col gap-4 px-6 py-4">
                    <div class="flex justify-between gap-4 font-semibold">
                        <p class="dark:text-gray-200">Total</p>
                        <p class="font-bold text-primary dark:text-secondary">
                            Rp {{ $formatCurrency(total) }}
                        </p>
                    </div>

                    <CustomButton
                        @click="showItemFormDialog"
                        :isFull="true"
                        :enable="selectedItems.length > 0"
                    >
                        Buat Penjualan
                    </CustomButton>
                </div>
            </DefaultCard>
        </div>

        <CustomDialog
            id="itemFormDialog"
            :show-cancel="true"
            @cancel="onItemFormDialogClosed"
        >
            <SalesForm
                :show-close-button="true"
                :item="{
                    note: null,
                    payment: 0,
                    sales_items: selectedItems,
                }"
                @close="onItemFormDialogClosed"
                @print="showPrintItemDialog"
                class="w-full sm:w-[400px]"
            />
        </CustomDialog>

        <CustomDialog id="printItemDialog" :show-cancel="true">
            <PrintSalesReceipt
                v-if="sales"
                :show-close-button="true"
                :sales="sales"
                @close="onPrintItemDialogClosed"
                class="w-full lg:w-[800px]"
            />
        </CustomDialog>
    </div>
</template>
