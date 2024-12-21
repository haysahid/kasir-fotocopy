<script setup>
import { ref, onUpdated, computed, watch, onMounted } from "vue";
import InputGroup from "@/components/Forms/InputGroup.vue";
import AlertWarning from "@/components/Alerts/AlertWarning.vue";
import CustomButton from "@/components/Forms/CustomButton.vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import CustomSwitch from "@/components/Forms/CustomSwitch.vue";
import { useOptionStore } from "@/stores/option";
import { usePlanStore } from "@/stores/plan";
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

const optionStore = useOptionStore();
const optionField = ref(null);

const planStore = usePlanStore();

const form = ref({
    name: "",
    price: 0,
    is_active: true,
    options: [],
});

const formValidation = ref({
    name: "",
    price: "",
    options: "",
});

function clearErrorMessage() {
    planStore.errorMessage = "";
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

    const response = await planStore.addItem(form.value);

    if (response && response.meta.code === 200) {
        close(true);
    } else {
        document
            .getElementById("pagetop")
            ?.scrollIntoView({ block: "start", behavior: "smooth" });
    }
}

async function updateItem() {
    if (!validateAddItem()) return;

    const response = await planStore.updateItem(props.item.id, form.value);

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

    if (form.value.price < 1) {
        formValidation.value.price = "Harga tidak boleh kosong";
        result = false;
    }

    if (form.value.options.length < 1) {
        formValidation.value.options = "Pilih minimal satu opsi";
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
    () => form.value.price,
    (newValue, oldValue) => {
        if (newValue && newValue > 0 && formValidation.value.price) {
            formValidation.value.price = "";
        }
    }
);

watch(
    () => form.value.options,
    (newValue, oldValue) => {
        if (newValue && newValue.length > 0 && formValidation.value.options) {
            formValidation.value.options = "";
        }
    }
);

onUpdated(() => {
    if (props.item) {
        form.value = {
            name: props.item.name,
            price: props.item.price,
            is_active: props.item.is_active,
            options: props.item.options.map((option) => option.id),
        };
    }
});

onMounted(() => {
    optionStore.getDropdown();
});

function close(value) {
    if (props.autoClearData) {
        form.value = {
            name: "",
            price: 0,
            is_active: true,
            options: [],
        };
    } else {
        form.value = {
            name: props.item.name,
            price: props.item.price,
            is_active: props.item.is_active,
            options: props.item.options.map((option) => option.id),
        };
    }

    emit("close", value);
    planStore.saveStatus = "";
    planStore.errorMessage = "";
    optionField.value?.clearSelectedOptions();
}
</script>

<template>
    <DefaultCard
        :card-title="props.item ? 'Ubah Paket' : 'Tambah Paket'"
        :show-close-button="props.showCloseButton"
        @close="close(false)"
    >
        <div class="p-6.5">
            <AlertWarning
                v-if="planStore.errorMessage"
                @close="clearErrorMessage"
                :description="planStore.errorMessage"
                class="mb-6"
            />

            <InputGroup
                v-model="form.name"
                id="name"
                label="Nama Paket"
                type="text"
                placeholder="Masukkan Nama Paket"
                :warning="formValidation.name"
            />

            <InputGroup
                v-model="form.price"
                id="price"
                label="Harga Paket"
                type="number"
                placeholder="Masukkan Harga Paket"
                :warning="formValidation.price"
            />

            <SearchSelectGroup
                ref="optionField"
                v-model="form.options"
                :options="
                    optionStore.dropdown.map((option) => {
                        return {
                            value: option.id,
                            text: option.name,
                        };
                    })
                "
                id="options"
                label="Opsi"
                type="multiple"
                placeholder="Pilih Opsi"
                :warning="formValidation.options"
            />

            <div class="flex flex-col-reverse mt-6 gap-x-4 sm:flex-row">
                <CustomButton
                    @click="close(false)"
                    :enable="planStore.saveStatus !== 'loading'"
                    color="bg-danger"
                    :is-full="true"
                    padding="p-3"
                    margin="mt-4"
                >
                    Batal
                </CustomButton>

                <CustomButton
                    @click="saveItem"
                    :loading="planStore.saveStatus === 'loading'"
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
