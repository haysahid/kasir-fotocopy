<script setup>
import { ref, onUpdated, computed, watch, onMounted } from "vue";
import InputGroup from "@/components/Forms/InputGroup.vue";
import AlertWarning from "@/components/Alerts/AlertWarning.vue";
import CustomButton from "@/components/Forms/CustomButton.vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import CustomSwitch from "@/components/Forms/CustomSwitch.vue";
import { usePaymentMethodStore } from "@/stores/payment_method";
import SearchSelectGroup from "@/components/Forms/SearchSelectGroup.vue";

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

const paymentMethodStore = usePaymentMethodStore();

const form = ref({
    name: "",
    description: "",
});

const formValidation = ref({
    name: "",
    description: "",
});

function clearErrorMessage() {
    paymentMethodStore.errorMessage = "";
}

async function saveItem() {
    if (props.item) {
        await updateItem();
    } else {
        await addItem();
    }
}

async function addItem() {
    if (!validateAddItem()) return;

    const response = await paymentMethodStore.addItem(form.value);

    if (response && response.meta.code === 200) {
        close(true);
    } else {
        document
            .getElementById("pagetop")
            ?.scrollIntoView({ block: "start", behavior: "smooth" });
    }
}

async function updateItem() {
    if (!validateUpdateItem()) return;

    const response = await paymentMethodStore.updateItem(
        props.item.id,
        form.value
    );

    if (response && response.meta.code === 200) {
        close(true);
    } else {
        document
            .getElementById("pagetop")
            ?.scrollIntoView({ block: "start", behavior: "smooth" });
    }
}

function validateAddItem() {
    let result = true;

    if (form.value.name.length < 1) {
        formValidation.value.name = "Nama lengkap tidak boleh kosong";
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

onUpdated(() => {
    if (props.item) {
        form.value = {
            name: props.item.name,
            description: props.item.description,
        };
    }
});

function close(value) {
    if (props.autoClearData) {
        form.value = {
            name: "",
            description: "",
        };
    } else {
        form.value = {
            name: props.item.name,
            description: props.item.description,
        };
    }

    emit("close", value);
    paymentMethodStore.saveStatus = "";
    paymentMethodStore.errorMessage = "";
}
</script>

<template>
    <DefaultCard
        :card-title="
            props.item ? 'Ubah Metode Pembayaran' : 'Tambah Metode Pembayaran'
        "
        :show-close-button="props.showCloseButton"
        @close="close(false)"
    >
        <div class="p-6.5">
            <AlertWarning
                v-if="paymentMethodStore.errorMessage"
                @close="clearErrorMessage"
                :description="paymentMethodStore.errorMessage"
                class="mb-6"
            />

            <InputGroup
                @enter="saveItem"
                v-model="form.name"
                id="name"
                label="Nama Metode Pembayaran"
                type="text"
                placeholder="Masukkan Nama Metode Pembayaran"
                :warning="formValidation.name"
            />

            <InputGroup
                @enter="saveItem"
                v-model="form.description"
                id="description"
                label="Deskripsi"
                type="text"
                placeholder="Masukkan Deskripsi"
                :warning="formValidation.description"
            />

            <div class="flex flex-col-reverse mt-6 gap-x-4 sm:flex-row">
                <CustomButton
                    @click="close(false)"
                    :enable="paymentMethodStore.saveStatus !== 'loading'"
                    color="bg-danger"
                    :is-full="true"
                    padding="p-3"
                    margin="mt-4"
                >
                    Batal
                </CustomButton>

                <CustomButton
                    @click="saveItem"
                    :loading="paymentMethodStore.saveStatus === 'loading'"
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
