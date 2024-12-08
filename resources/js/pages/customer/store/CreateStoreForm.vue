<script setup>
import { ref, inject, computed, watch, onMounted } from "vue";
import InputGroup from "@/components/Forms/InputGroup.vue";
import AlertWarning from "@/components/Alerts/AlertWarning.vue";
import DefaultAuthCard from "@/components/authentication/DefaultAuthCard.vue";
import CustomButton from "@/components/Forms/CustomButton.vue";
import SelectGroup from "@/components/Forms/SelectGroup.vue";

import { Link } from "@inertiajs/inertia-vue3";

const axios = inject("axios");

const form = ref({
    name: "",
    description: "",
    address: "",
});

const formValidation = ref({
    name: "",
    description: "",
    address: "",
});

const errorMessage = ref("");
const createStoreStatus = ref("");

function clearErrorMessage() {
    errorMessage.value = "";
}

async function createStore() {
    if (!validate()) return;

    createStoreStatus.value = "loading";

    try {
        const response = await axios.post("/api/store", form.value, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem("access_token")}`,
            },
        });

        console.log(response.data);

        createStoreStatus.value = "success";

        window.location = route("dashboard");
    } catch (error) {
        errorMessage.value =
            error.response?.data?.meta?.message ?? "Terjadi kesalahan";
        createStoreStatus.value = "error";
    }
}

function validate() {
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

    return result;
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
</script>

<template>
    <DefaultAuthCard title="Buat Toko" :centerTitle="true">
        <form action="#">
            <AlertWarning
                v-if="errorMessage"
                @close="clearErrorMessage"
                :description="errorMessage"
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
                @enter="createStore"
                v-model="form.address"
                id="address"
                label="Alamat"
                type="text"
                placeholder="Alamat"
                :warning="formValidation.address"
            />

            <CustomButton
                @click="createStore"
                :loading="createStoreStatus === 'loading'"
                :enable="true"
                color="bg-primary"
                class="mt-6"
            >
                Buat Toko
            </CustomButton>
        </form>
    </DefaultAuthCard>
</template>
