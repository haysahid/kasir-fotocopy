<script setup>
import { ref, onUpdated, watch, onMounted, computed } from "vue";
import InputGroup from "@/components/Forms/InputGroup.vue";
import AlertWarning from "@/components/Alerts/AlertWarning.vue";
import CustomButton from "@/components/Forms/CustomButton.vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import { usePurchaseStore } from "@/stores/purchase";
import formatCurrency from "@/plugins/currency_formatter";

const props = defineProps({
    showCloseButton: {
        type: Boolean,
    },
    item: {
        type: Object,
        default: {
            note: "",
            payment: "",
            purchase_items: [],
        },
    },
    autoClearData: {
        type: Boolean,
        default: true,
    },
});
const emit = defineEmits(["close"]);

const purchaseStore = usePurchaseStore();

const form = ref({
    note: "",
    payment: "",
    purchase_items: [],
});

const formValidation = ref({
    note: "",
    payment: "",
});

function clearErrorMessage() {
    purchaseStore.errorMessage = "";
}

async function saveItem() {
    form.value.note = form.value.note || null;

    if (props.item.id) {
        updateItem();
    } else {
        addItem();
    }
}

async function addItem() {
    if (!validateAddItem()) return;

    const data = {
        ...form.value,
        payment: currencyToNumber(form.value.payment),
        purchase_items: form.value.purchase_items.map((item) => ({
            product_id: item.id,
            quantity: item.quantity,
            item_price: item.item_price,
        })),
    };

    const response = await purchaseStore.addItem(data);

    if (response.meta.code === 201) {
        close(true);
    }
}

async function updateItem() {
    if (!validateUpdateItem()) return;

    const data = {
        ...form.value,
        payment: currencyToNumber(form.value.payment),
    };

    const response = await purchaseStore.updateItem(props.item.id, data);

    if (response.meta.code === 200) {
        close(true);
    }
}

function validateAddItem() {
    let result = true;

    if (form.value.payment.length < 1) {
        formValidation.value.payment = "Harga beli tidak boleh kosong";
        result = false;
    }

    if (form.value.purchase_items.length < 1) {
        purchaseStore.errorMessage = "Produk / jasa tidak boleh kosong";
        result = false;
    }

    return result;
}

function validateUpdateItem() {
    return validateAddItem();
}

watch(
    () => form.value.payment,
    (newValue, oldValue) => {
        if (newValue && newValue.length > 0 && formValidation.value.payment) {
            formValidation.value.payment = "";
        }
    }
);

const total = computed(() => {
    return props.item.purchase_items.reduce((acc, item) => {
        return acc + item.quantity * item.item_price;
    }, 0);
});

const paymentReturn = computed(() => {
    return currencyToNumber(form.value.payment) - total.value;
});

function currencyToNumber(value) {
    if (!value) return 0;

    return value.replace(/[^0-9]/g, "");
}

function getForm() {
    form.value.note = props.item.note;
    form.value.payment = formatCurrency(props.item.payment);
    form.value.purchase_items = props.item.purchase_items;
}

function clearForm() {
    form.value = {
        note: "",
        payment: "",
        purchase_items: [],
    };
}

onUpdated(() => {
    if (props.item) {
        getForm();
    }
});

onMounted(() => {
    if (props.item) {
        getForm();
    }
});

function close(value) {
    if (props.autoClearData) {
        clearForm();
    }

    purchaseStore.saveStatus = "";
    emit("close", value);
}
</script>

<template>
    <DefaultCard
        :card-title="props.item.id ? 'Ubah Pembelian' : 'Tambah Pembelian'"
        :show-close-button="props.showCloseButton"
        @close="close(false)"
    >
        <div class="p-6.5">
            <AlertWarning
                v-if="purchaseStore.errorMessage"
                @close="clearErrorMessage"
                :description="purchaseStore.errorMessage"
                class="mb-6"
            />

            <InputGroup
                v-model="form.payment"
                @enter="saveItem"
                id="payment"
                label="Pembayaran"
                type="currency"
                placeholder="Masukkan pembayaran"
                :autoFocus="true"
                :warning="formValidation.payment"
            />

            <InputGroup
                v-model="form.note"
                @enter="saveItem"
                id="note"
                label="Catatan"
                type="text"
                placeholder="Masukkan catatan"
                :warning="formValidation.note"
            />

            <div class="flex justify-between gap-4 font-semibold">
                <p class="dark:text-gray-200">Total</p>
                <p class="font-bold text-primary dark:text-secondary">
                    Rp {{ $formatCurrency(total) }}
                </p>
            </div>

            <div class="flex justify-between gap-4">
                <p class="dark:text-gray-200">Pembayaran</p>
                <p class="text-primary dark:text-secondary">
                    Rp {{ form.payment }}
                </p>
            </div>

            <div class="flex justify-between gap-4">
                <p class="dark:text-gray-200">Kembalian</p>
                <p class="text-primary dark:text-secondary">
                    Rp {{ $formatCurrency(paymentReturn) }}
                </p>
            </div>

            <div class="flex flex-col-reverse gap-x-4 sm:flex-row">
                <CustomButton
                    @click="close(false)"
                    :enable="purchaseStore.saveStatus !== 'loading'"
                    color="bg-danger"
                    :is-full="true"
                    padding="p-3"
                    margin="mt-4"
                >
                    Batal
                </CustomButton>

                <CustomButton
                    @click="saveItem"
                    :loading="purchaseStore.saveStatus === 'loading'"
                    color="bg-primary"
                    :is-full="true"
                    padding="p-3"
                    margin="mt-4"
                >
                    Simpan
                </CustomButton>
            </div>
        </div>
    </DefaultCard>
</template>
