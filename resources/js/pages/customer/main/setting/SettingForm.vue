<script setup>
import { ref, onUpdated, watch, onMounted, computed } from "vue";
import InputGroup from "@/components/Forms/InputGroup.vue";
import AlertWarning from "@/components/Alerts/AlertWarning.vue";
import CustomButton from "@/components/Forms/CustomButton.vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import SuccessDialog from "@/components/Dialogs/SuccessDialog.vue";
import { useStoreConfigStore } from "@/stores/store-config";

const storeConfigStore = useStoreConfigStore();

const form = ref({
    store_acronym: "",
});

const formValidation = ref({
    store_acronym: "",
});

function clearErrorMessage() {
    storeConfigStore.errorMessage = "";
}

async function getConfig() {
    await storeConfigStore.getConfig();

    form.value = storeConfigStore.data;
}

async function saveItem() {
    storeConfigStore.saveItem(form.value);
}

function validateAddItem() {
    let result = true;

    if (form.value.sales_items.length < 1) {
        storeConfigStore.errorMessage = "Produk / jasa tidak boleh kosong";
        result = false;
    }

    return result;
}

function validateUpdateItem() {
    return validateAddItem();
}

onMounted(() => {
    getConfig();
});
</script>

<template>
    <DefaultCard>
        <div class="p-6.5">
            <AlertWarning
                v-if="storeConfigStore.errorMessage"
                @close="clearErrorMessage"
                :description="storeConfigStore.errorMessage"
                class="mb-6"
            />

            <InputGroup
                v-model="form.store_acronym"
                id="store_acronym"
                label="Akronim "
                type="text"
                placeholder="Masukkan akronim toko"
                :autoFocus="true"
                :warning="formValidation.store_acronym"
            />

            <div class="flex flex-col-reverse gap-x-4 sm:flex-row">
                <CustomButton
                    @click="saveItem"
                    :loading="storeConfigStore.saveStatus === 'loading'"
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
