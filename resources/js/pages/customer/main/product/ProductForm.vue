<script setup>
import { ref, onUpdated, watch, onMounted } from "vue";
import InputGroup from "@/components/Forms/InputGroup.vue";
import CustomDatePicker from "@/components/Forms/CustomDatePicker.vue";
import AlertWarning from "@/components/Alerts/AlertWarning.vue";
import CustomButton from "@/components/Forms/CustomButton.vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import { useProductStore } from "@/stores/product";
import InputImageGroup from "@/components/Forms/InputImageGroup.vue";
import { useConfigStore } from "@/stores/config";
import formatCurrency from "@/plugins/currency_formatter";

const props = defineProps({
    showCloseButton: {
        type: Boolean,
    },
    item: {
        type: Object,
    },
    autoClearData: {
        type: Boolean,
        default: true,
    },
});
const emit = defineEmits(["close"]);

const productStore = useProductStore();
const configStore = useConfigStore();

const form = ref({
    code: "",
    name: "",
    description: "",
    purchase_price: "",
    selling_price: "",
    initial_stock: "",
    unit: "",
    category: "",
    expired_at: "",
    image: "",
});

const formValidation = ref({
    code: "",
    name: "",
    description: "",
    purchase_price: "",
    selling_price: "",
    initial_stock: "",
    unit: "",
    category: "",
    expired_at: "",
    image: "",
});

const imageField = ref(null);

function clearErrorMessage() {
    productStore.errorMessage = "";
}

async function saveItem() {
    if (props.item) {
        updateItem();
    } else {
        addItem();
    }
}

async function addItem() {
    if (!validateAddItem()) return;

    const data = {
        ...form.value,
        purchase_price: form.value.purchase_price.replace(/[^0-9]/g, ""),
        selling_price: form.value.selling_price.replace(/[^0-9]/g, ""),
    };

    const response = await productStore.addItem(data);

    if (response.meta.code === 201) {
        close(true);
    }
}

async function updateItem() {
    if (!validateUpdateItem()) return;

    const data = {
        ...form.value,
        purchase_price: form.value.purchase_price.replace(/[^0-9]/g, ""),
        selling_price: form.value.selling_price.replace(/[^0-9]/g, ""),
    };

    const response = await productStore.updateItem(props.item.id, data);

    if (response.meta.code === 200) {
        close(true);
    }
}

function validateAddItem() {
    let result = true;

    if (form.value.name.length < 1) {
        formValidation.value.name = "Nama produk tidak boleh kosong";
        result = false;
    }

    if (form.value.description.length < 1) {
        formValidation.value.description =
            "Deskripsi produk tidak boleh kosong";
        result = false;
    }

    if (form.value.purchase_price.length < 1) {
        formValidation.value.purchase_price = "Harga beli tidak boleh kosong";
        result = false;
    }

    if (form.value.selling_price.length < 1) {
        formValidation.value.selling_price = "Harga jual tidak boleh kosong";
        result = false;
    }

    if (form.value.initial_stock.length < 1) {
        formValidation.value.initial_stock = "Stok awal tidak boleh kosong";
        result = false;
    }

    if (form.value.unit.length < 1) {
        formValidation.value.unit = "Satuan tidak boleh kosong";
        result = false;
    }

    if (form.value.category.length < 1) {
        formValidation.value.category = "Kategori tidak boleh kosong";
        result = false;
    }

    return result;
}

function validateUpdateItem() {
    return validateAddItem();
}

watch(
    () => form.value.name,
    (newValue, oldValue) => {
        if (newValue && newValue.length > 0 && formValidation.value.name) {
            formValidation.value.name = "";
        }
    }
);

watch(
    () => form.value.description,
    (newValue, oldValue) => {
        if (
            newValue &&
            newValue.length > 0 &&
            formValidation.value.description
        ) {
            formValidation.value.description = "";
        }
    }
);

watch(
    () => form.value.purchase_price,
    (newValue, oldValue) => {
        if (
            newValue &&
            newValue.length > 0 &&
            formValidation.value.purchase_price
        ) {
            if (!form.value.purchase_price.match(/^\d+$/)) {
                formValidation.value.purchase_price =
                    "Harga jual harus berupa angka";
                result = false;
            } else {
                formValidation.value.purchase_price = "";
            }
        }
    }
);

watch(
    () => form.value.selling_price,
    (newValue, oldValue) => {
        if (
            newValue &&
            newValue.length > 0 &&
            formValidation.value.selling_price
        ) {
            if (!form.value.selling_price.match(/^\d+$/)) {
                formValidation.value.selling_price =
                    "Harga jual harus berupa angka";
                result = false;
            } else {
                formValidation.value.selling_price = "";
            }
        }
    }
);

watch(
    () => form.value.initial_stock,
    (newValue, oldValue) => {
        if (
            newValue &&
            newValue.length > 0 &&
            formValidation.value.initial_stock
        ) {
            formValidation.value.initial_stock = "";
        }
    }
);

watch(
    () => form.value.unit,
    (newValue, oldValue) => {
        if (newValue && newValue.length > 0 && formValidation.value.unit) {
            formValidation.value.unit = "";
        }
    }
);

function getForm() {
    form.value.code = props.item.code;
    form.value.name = props.item.name;
    form.value.description = props.item.description;
    form.value.purchase_price = formatCurrency(props.item.purchase_price);
    form.value.selling_price = formatCurrency(props.item.selling_price);
    form.value.initial_stock = props.item.initial_stock;
    form.value.unit = props.item.unit;
    form.value.category = props.item.category;
    form.value.expired_at = props.item.expired_at;
    const images = props.item.product_images;
    form.value.image =
        images.length > 0 ? configStore.getImageUrl(images[0].image) : "";

    imageField.value?.clearImage();
}

function clearForm() {
    form.value = {
        code: "",
        name: "",
        description: "",
        purchase_price: "",
        selling_price: "",
        initial_stock: "",
        unit: "",
        category: "",
        expired_at: "",
        image: "",
    };

    formValidation.value = {
        code: "",
        name: "",
        description: "",
        purchase_price: "",
        selling_price: "",
        initial_stock: "",
        unit: "",
        category: "",
        expired_at: "",
        image: "",
    };

    imageField.value?.clearImage();
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
    } else {
        getForm();
    }

    emit("close", value);
    productStore.errorMessage = "";
    productStore.saveStatus = "";
}
</script>

<template>
    <DefaultCard
        :card-title="props.item ? 'Ubah Produk / Jasa' : 'Tambah Produk / Jasa'"
        :show-close-button="props.showCloseButton"
        @close="close(false)"
    >
        <div class="p-6.5">
            <AlertWarning
                v-if="productStore.errorMessage"
                @close="clearErrorMessage"
                :description="productStore.errorMessage"
                class="mb-6"
            />

            <InputImageGroup
                ref="imageField"
                v-model="form.image"
                id="image"
                label="Gambar"
                type="file"
                placeholder="Pilih gambar"
                :warning="formValidation.image"
                class="mb-4"
            />

            <InputGroup
                v-model="form.code"
                id="code"
                label="Barcode"
                type="text"
                placeholder="Masukkan barcode"
                :warning="formValidation.code"
            />

            <InputGroup
                v-model="form.name"
                id="name"
                label="Nama"
                type="text"
                placeholder="Masukkan nama produk"
                :warning="formValidation.name"
            />

            <InputGroup
                v-model="form.description"
                id="description"
                label="Deskripsi"
                type="text"
                placeholder="Masukkan deskripsi produk"
                :warning="formValidation.description"
            />

            <InputGroup
                v-model="form.purchase_price"
                id="purchase_price"
                label="Harga Beli"
                type="currency"
                placeholder="Masukkan harga beli"
                :warning="formValidation.purchase_price"
            />

            <InputGroup
                v-model="form.selling_price"
                id="selling_price"
                label="Harga Jual"
                type="currency"
                placeholder="Masukkan harga jual"
                :warning="formValidation.selling_price"
            />

            <InputGroup
                v-model="form.initial_stock"
                id="initial_stock"
                label="Stok Awal"
                type="currency"
                placeholder="Masukkan stok awal"
                :warning="formValidation.initial_stock"
            />

            <InputGroup
                v-model="form.unit"
                id="unit"
                label="Satuan"
                type="text"
                placeholder="Masukkan satuan"
                :warning="formValidation.unit"
            />

            <InputGroup
                v-model="form.category"
                id="category"
                label="Kategori"
                type="text"
                placeholder="Masukkan kategori"
                :warning="formValidation.category"
            />

            <CustomDatePicker
                v-model="form.expired_at"
                id="expired_at"
                label="Kadaluarsa"
                placeholder="yyyy-mm-dd"
                :warning="formValidation.expired_at"
            />

            <div class="flex flex-col-reverse mt-2 gap-x-4 sm:flex-row">
                <CustomButton
                    @click="close(false)"
                    :enable="productStore.saveStatus !== 'loading'"
                    color="bg-danger"
                    :is-full="true"
                    padding="p-3"
                    margin="mt-4"
                >
                    Batal
                </CustomButton>

                <CustomButton
                    @click="saveItem"
                    :loading="productStore.saveStatus === 'loading'"
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
