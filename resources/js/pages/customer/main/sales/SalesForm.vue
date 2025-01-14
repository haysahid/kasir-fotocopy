<script setup>
import { ref, onUpdated, watch, onMounted, computed } from "vue";
import InputGroup from "@/components/Forms/InputGroup.vue";
import AlertWarning from "@/components/Alerts/AlertWarning.vue";
import CustomButton from "@/components/Forms/CustomButton.vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import { useSalesStore } from "@/stores/sales";
import SuccessDialog from "@/components/Dialogs/SuccessDialog.vue";

const props = defineProps({
    showCloseButton: {
        type: Boolean,
    },
    item: {
        type: Object,
        default: {
            note: "",
            payment: 0,
            sales_items: [],
        },
    },
    autoClearData: {
        type: Boolean,
        default: true,
    },
});
const emit = defineEmits(["close", "print"]);

const salesStore = useSalesStore();

const form = ref({
    note: "",
    payment: 0,
    sales_items: [],
});

const formValidation = ref({
    note: "",
    payment: "",
});

const sales = ref(0);

function clearErrorMessage() {
    salesStore.errorMessage = "";
}

async function saveItem() {
    sales.value = 0;

    if (props.item.id) {
        updateItem();
    } else {
        addItem();
    }
}

async function addItem() {
    if (!validateAddItem()) return;

    form.value.note = form.value.note || null;

    console.log(form.value.sales_items);

    const updatedForm = {
        ...form.value,
        sales_items: form.value.sales_items.map((item) => ({
            product_id: item.id,
            quantity: item.quantity,
            item_price: item.item_price,
        })),
    };

    const response = await salesStore.addItem(updatedForm);

    sales.value = response.result;
}

async function updateItem() {
    if (!validateUpdateItem()) return;

    form.value.note = form.value.note || null;

    const response = await salesStore.updateItem(props.item.id, form.value);

    if (response.meta.code === 200) {
        close(true);
    }
}

function onSuccesDialogClose() {
    close(true);
}

function validateAddItem() {
    let result = true;

    // if (!form.value.payment.match(/^\d+$/)) {
    //     formValidation.value.payment = "Pembayaran harus berupa angka";
    //     result = false;
    // }

    if (form.value.sales_items.length < 1) {
        salesStore.errorMessage = "Produk / jasa tidak boleh kosong";
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
            if (!form.value.payment.match(/^\d+$/)) {
                formValidation.value.payment = "Pembayaran harus berupa angka";
                result = false;
            } else {
                formValidation.value.payment = "";
            }
        }
    }
);

const total = computed(() => {
    return props.item.sales_items.reduce((acc, item) => {
        return acc + item.quantity * item.item_price;
    }, 0);
});

const paymentReturn = computed(() => {
    return form.value.payment - total.value;
});

onUpdated(() => {
    if (props.item) {
        form.value.note = props.item.note;
        form.value.payment = props.item.payment;
        form.value.sales_items = props.item.sales_items;
    }
});

onMounted(() => {
    if (props.item) {
        form.value.note = props.item.note;
        form.value.payment = props.item.payment;
        form.value.sales_items = props.item.sales_items;
    }
});

function close(value) {
    if (props.autoClearData) {
        form.value = {
            note: "",
            payment: 0,
            sales_items: [],
        };
    }

    emit("close", value);
    salesStore.clearSalesStore();
}

function print() {
    emit("print", sales.value);
    close(true);
}
</script>

<template>
    <SuccessDialog
        v-if="salesStore.saveStatus === 'success' && sales"
        title="Berhasil!"
        description="Penjualan berhasil ditambahkan"
        :sales="sales"
        class="max-sm:w-full sm:min-w-[400px] max-w-[400px]"
        @close="onSuccesDialogClose"
    >
        <CustomButton
            color="bg-green-600"
            :is-full="true"
            padding="py-2.5 px-6"
            @click="print"
        >
            Cetak Struk
        </CustomButton>
    </SuccessDialog>

    <DefaultCard
        v-else
        :card-title="props.item.id ? 'Ubah Penjualan' : 'Tambah Penjualan'"
        :show-close-button="props.showCloseButton"
        @close="close(false)"
    >
        <div class="p-6.5">
            <AlertWarning
                v-if="salesStore.errorMessage"
                @close="clearErrorMessage"
                :description="salesStore.errorMessage"
                class="mb-6"
            />

            <InputGroup
                v-model="form.payment"
                @enter="saveItem"
                id="payment"
                label="Pembayaran"
                type="number"
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
                    Rp {{ $formatCurrency(form.payment) }}
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
                    :enable="salesStore.saveStatus !== 'loading'"
                    color="bg-danger"
                    :is-full="true"
                    padding="p-3"
                    margin="mt-4"
                >
                    Batal
                </CustomButton>

                <CustomButton
                    @click="saveItem"
                    :loading="salesStore.saveStatus === 'loading'"
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
