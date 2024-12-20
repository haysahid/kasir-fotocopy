<script setup>
import { ref, onUpdated, computed, watch } from "vue";
import InputGroup from "@/components/Forms/InputGroup.vue";
import AlertWarning from "@/components/Alerts/AlertWarning.vue";
import CustomButton from "@/components/Forms/CustomButton.vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import CustomSwitch from "@/components/Forms/CustomSwitch.vue";
import { useStoreStore } from "@/stores/store";

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

const storeStore = useStoreStore();

const form = ref({
    name: "",
    description: "",
    address: "",
    phone: "",
});

const formValidation = ref({
    name: "",
    description: "",
    address: "",
    phone: "",
});

function clearErrorMessage() {
    storeStore.errorMessage = "";
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

    const response = await storeStore.addItem(form.value);

    if (response.meta.code === 201) {
        close(true);
    }
}

async function updateItem() {
    if (!validateUpdateItem()) return;

    const response = await storeStore.updateItem(props.item.id, form.value);

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
        formValidation.value.name = "Nama toko tidak boleh kosong";
        result = false;
    }

    if (form.value.description.length < 1) {
        formValidation.value.description = "Deskripsi toko tidak boleh kosong";
        result = false;
    }

    if (form.value.address.length < 1) {
        formValidation.value.address = "Alamat toko tidak boleh kosong";
        result = false;
    }

    if (form.value.phone && form.value.phone.length < 1) {
        formValidation.value.phone = "Nomor telepon tidak boleh kosong";
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
    () => form.value.phone,
    (newValue, oldValue) => {
        if (newValue && newValue.length > 0 && formValidation.value.phone) {
            formValidation.value.phone = "";
        }
    }
);

onUpdated(() => {
    if (props.item) {
        form.value = {
            name: props.item.name,
            description: props.item.description,
            address: props.item.address,
            phone: props.item.phone,
        };
    }
});

function close(value) {
    if (props.autoClearData) {
        form.value = {
            name: "",
            description: "",
            address: "",
            phone: "",
        };
    } else {
        form.value = {
            name: props.item.name,
            description: props.item.description,
            address: props.item.address,
            phone: props.item.phone,
        };
    }

    emit("close", value);
    storeStore.saveStatus = "";
    storeStore.errorMessage = "";
}
</script>

<template>
    <DefaultCard
        :card-title="props.item ? 'Ubah Toko' : 'Tambah Toko'"
        :show-close-button="props.showCloseButton"
        @close="close(false)"
    >
        <div class="p-6.5">
            <AlertWarning
                v-if="storeStore.errorMessage"
                @close="clearErrorMessage"
                :description="storeStore.errorMessage"
                class="mb-6"
            />

            <InputGroup
                v-model="form.name"
                id="name"
                label="Nama Toko"
                type="text"
                placeholder="Masukkan Nama Toko"
                :warning="formValidation.name"
            />

            <InputGroup
                v-model="form.description"
                id="description"
                label="Deskripsi Toko"
                type="text"
                placeholder="Masukkan Deskripsi Toko"
                :warning="formValidation.description"
            />

            <InputGroup
                @enter="saveItem"
                v-model="form.address"
                id="address"
                label="Alamat"
                type="text"
                placeholder="Alamat"
                :warning="formValidation.address"
            />

            <InputGroup
                @enter="saveItem"
                v-model="form.phone"
                id="phone"
                label="Nomor Telepon"
                type="text"
                placeholder="Nomor Telepon"
                :warning="formValidation.phone"
            />

            <div class="flex flex-col-reverse mt-6 gap-x-4 sm:flex-row">
                <CustomButton
                    @click="close(false)"
                    :enable="storeStore.saveStatus !== 'loading'"
                    color="bg-danger"
                    :is-full="true"
                    padding="p-3"
                    margin="mt-4"
                >
                    Batal
                </CustomButton>

                <CustomButton
                    @click="saveItem"
                    :loading="storeStore.saveStatus === 'loading'"
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
