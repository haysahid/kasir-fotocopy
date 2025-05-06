<script setup>
import { ref, onUpdated, watch, onMounted, computed } from "vue";
import InputGroup from "@/components/Forms/InputGroup.vue";
import AlertWarning from "@/components/Alerts/AlertWarning.vue";
import CustomButton from "@/components/Forms/CustomButton.vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import InputImageGroup from "@/components/Forms/InputImageGroup.vue";
import { useConfigStore } from "@/stores/config";
import { useCategoryStore } from "@/stores/category";

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

const categoryStore = useCategoryStore();
const configStore = useConfigStore();

const form = ref({
    name: "",
    description: "",
    image: "",
});

const selectedCategories = computed(() => {
    if (!form.value.category) {
        return [];
    }

    return form.value.category.split(", ");
});

const formValidation = ref({
    name: "",
    description: "",
    image: "",
});

const imageField = ref(null);

function clearErrorMessage() {
    categoryStore.errorMessage = "";
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
    };

    const response = await categoryStore.addItem(data);

    if (response.status === 201) {
        close(true);
    } else if (response.status === 422) {
        const errors = response.data.errors;
        getFormValidationErrors(errors);
    }
}

async function updateItem() {
    if (!validateUpdateItem()) return;

    const data = {
        ...form.value,
    };

    const response = await categoryStore.updateItem(props.item.id, data);

    if (response.status === 200) {
        close(true);
    } else if (response.status === 422) {
        const errors = response.data.errors;
        getFormValidationErrors(errors);
    }
}

function validateAddItem() {
    let result = true;

    if (form.value.name.length < 1) {
        formValidation.value.name = "Nama produk tidak boleh kosong";
        result = false;
    }

    return result;
}

function validateUpdateItem() {
    return validateAddItem();
}

function getFormValidationErrors(errors) {
    if (errors.name) {
        formValidation.value.name = errors.name[0];
    }

    if (errors.description) {
        formValidation.value.description = errors.description[0];
    }

    if (errors.image) {
        formValidation.value.image = errors.image[0];
    }
}

watch(
    () => form.value.name,
    (newValue, oldValue) => {
        if (newValue && newValue.length > 0 && formValidation.value.name) {
            formValidation.value.name = "";
        }
    }
);

function getForm() {
    form.value.name = props.item.name;
    form.value.description = props.item.description;
    form.value.image = props.item.image
        ? configStore.getImageUrl(props.item.image)
        : "";

    imageField.value?.clearImage();
}

function clearForm() {
    form.value = {
        name: "",
        description: "",
        image: "",
    };

    formValidation.value = {
        name: "",
        description: "",
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
    categoryStore.errorMessage = "";
    categoryStore.saveStatus = "";
}
</script>

<template>
    <DefaultCard
        :card-title="props.item ? 'Ubah Kategori' : 'Tambah Kategori'"
        :show-close-button="props.showCloseButton"
        @close="close(false)"
    >
        <div class="p-6.5">
            <AlertWarning
                v-if="categoryStore.errorMessage"
                @close="clearErrorMessage"
                :description="categoryStore.errorMessage"
                class="mb-6"
            />

            <div class="flex flex-col gap-4 lg:flex-row lg:gap-6">
                <div class="w-full">
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
                </div>

                <div class="w-full">
                    <InputGroup
                        v-model="form.name"
                        id="name"
                        label="Nama"
                        type="text"
                        placeholder="Masukkan nama produk"
                        :warning="formValidation.name"
                        @enter="saveItem"
                    />

                    <InputGroup
                        v-model="form.description"
                        id="description"
                        label="Deskripsi"
                        type="text"
                        placeholder="Masukkan deskripsi produk"
                        :warning="formValidation.description"
                        @enter="saveItem"
                    />

                    <div class="flex flex-col-reverse mt-2 gap-x-4 sm:flex-row">
                        <CustomButton
                            @click="close(false)"
                            :enable="categoryStore.saveStatus !== 'loading'"
                            color="bg-danger"
                            :is-full="true"
                            padding="p-3"
                            margin="mt-4"
                        >
                            Batal
                        </CustomButton>

                        <CustomButton
                            @click="saveItem"
                            :loading="categoryStore.saveStatus === 'loading'"
                            color="bg-primary"
                            :is-full="true"
                            padding="p-3"
                            margin="mt-4"
                        >
                            Simpan
                        </CustomButton>
                    </div>
                </div>
            </div>
        </div>
    </DefaultCard>
</template>
