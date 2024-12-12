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
    email: "",
    password: "",
    passwordConfirmation: "",
    phone: "",
    address: "",
});

const formValidation = ref({
    name: "",
    email: "",
    password: "",
    passwordConfirmation: "",
    phone: "",
    address: "",
});

const errorMessage = ref("");
const registerStatus = ref("");

function clearErrorMessage() {
    errorMessage.value = "";
}

async function register() {
    if (!validate()) return;

    registerStatus.value = "loading";

    try {
        const response = await axios.post("/api/auth/register", form.value);

        localStorage.setItem("access_token", response.data.result.access_token);

        registerStatus.value = "success";

        window.location = route("create-store");
    } catch (error) {
        errorMessage.value =
            error.response.data?.message ?? "Terjadi kesalahan";
        registerStatus.value = "error";
    }
}

function validate() {
    let result = true;

    if (form.value.name.length < 1) {
        formValidation.value.name = "Nama lengkap tidak boleh kosong";
        result = false;
    }

    if (form.value.email.length < 1) {
        formValidation.value.email = "Email tidak boleh kosong";
        result = false;
    } else if (
        !form.value.email.match(/\S+@\S+\.\S+/) ||
        form.value.email.includes(" ")
    ) {
        formValidation.value.email = "Email tidak valid";
    }

    if (form.value.password.length < 1) {
        formValidation.value.password = "Password tidak boleh kosong";
        result = false;
    } else if (form.value.password.length < 8) {
        formValidation.value.password = "Password minimal 8 karakter";
        result = false;
    }

    if (form.value.passwordConfirmation.length < 1) {
        formValidation.value.passwordConfirmation =
            "Konfirmasi password tidak boleh kosong";
        result = false;
    }

    if (form.value.phone.length < 1) {
        formValidation.value.phone = "Nomor HP tidak boleh kosong";
        result = false;
    } else if (!form.value.phone.match(/^\d+$/)) {
        formValidation.value.phone = "Nomor HP tidak valid";
        result = false;
    }

    return result;
}

const disablePasswordConfirmation = computed(
    () => form.value.password.length < 1
);

watch(
    () => form.value.name,
    (newValue, oldValue) => {
        if (newValue && newValue.length > 0 && formValidation.value.name) {
            formValidation.value.name = "";
        }
    }
);

watch(
    () => form.value.email,
    (newValue, oldValue) => {
        if (newValue && newValue.length > 0 && formValidation.value.email) {
            formValidation.value.email = "";
        }

        if (
            newValue &&
            newValue.length > 0 &&
            (!newValue.match(/\S+@\S+\.\S+/) || newValue.includes(" "))
        ) {
            formValidation.value.email = "Email tidak valid";
        } else {
            formValidation.value.email = "";
        }
    }
);

watch(
    () => form.value.password,
    (newValue, oldValue) => {
        if (form.value.password.length > 0 && form.value.password.length < 8) {
            formValidation.value.password = "Password minimal 8 karakter";
        } else if (
            newValue &&
            newValue.length > 0 &&
            formValidation.value.password
        ) {
            formValidation.value.password = "";
        } else {
            formValidation.value.password = "";
        }

        if (
            newValue &&
            newValue.length > 0 &&
            form.value.passwordConfirmation.length > 0 &&
            newValue !== form.value.passwordConfirmation
        ) {
            formValidation.value.passwordConfirmation =
                "Konfirmasi password tidak sama";
        } else {
            formValidation.value.passwordConfirmation = "";
        }
    }
);

watch(
    () => form.value.passwordConfirmation,
    (newValue, oldValue) => {
        if (
            newValue &&
            newValue.length > 0 &&
            form.value.password.length > 0 &&
            newValue !== form.value.password
        ) {
            formValidation.value.passwordConfirmation =
                "Konfirmasi password tidak sama";
        } else {
            formValidation.value.passwordConfirmation = "";
        }
    }
);

watch(
    () => form.value.phone,
    (newValue, oldValue) => {
        if (newValue && newValue.length > 0 && formValidation.value.phone) {
            formValidation.value.phone = "";
        }

        if (
            newValue &&
            newValue.length > 0 &&
            (!newValue.match(/^\d+$/) || newValue.includes(" "))
        ) {
            formValidation.value.phone = "Nomor HP tidak valid";
        } else {
            formValidation.value.phone = "";
        }
    }
);
</script>

<template>
    <DefaultAuthCard title="Pendaftaran" :centerTitle="true">
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
                label="Nama Lengkap"
                type="text"
                placeholder="Masukkan Nama Lengkap"
                :warning="formValidation.name"
            />

            <InputGroup
                v-model="form.email"
                id="email"
                label="Alamat Email"
                type="email"
                placeholder="Masukkan Alamat Email"
                :warning="formValidation.email"
            />

            <InputGroup
                v-model="form.password"
                id="password"
                label="Password"
                type="password"
                placeholder="Masukkan Password"
                :warning="formValidation.password"
            />

            <InputGroup
                v-model="form.passwordConfirmation"
                id="passwordConfirmation"
                label="Konfirmasi Password"
                type="password"
                placeholder="Masukkan Ulang Password"
                :warning="formValidation.passwordConfirmation"
                :disabled="disablePasswordConfirmation"
            />

            <InputGroup
                @enter="register"
                v-model="form.phone"
                id="phone"
                label="Nomor HP"
                type="text"
                placeholder="Masukkan Nomor HP"
                :warning="formValidation.phone"
            />

            <InputGroup
                @enter="register"
                v-model="form.address"
                id="address"
                label="Alamat"
                type="text"
                placeholder="Masukkan Alamat"
                :warning="formValidation.address"
            />

            <CustomButton
                @click="register"
                :loading="registerStatus === 'loading'"
                :enable="true"
                color="bg-primary"
                class="mt-6"
            >
                Daftar
            </CustomButton>

            <div class="mt-6 text-center">
                <p class="text-sm">
                    Sudah punya akun?
                    <Link
                        :href="route('login')"
                        class="text-primary hover:font-bold"
                    >
                        Masuk
                    </Link>
                </p>
            </div>
        </form>
    </DefaultAuthCard>
</template>
