<script setup>
import { ref, inject, watch } from "vue";
import { useSidebarStore } from "@/stores/sidebar";
import InputGroup from "@/components/Forms/InputGroup.vue";
import AlertWarning from "@/components/Alerts/AlertWarning.vue";
import DefaultAuthCard from "@/components/authentication/DefaultAuthCard.vue";
import CustomButton from "@/components/Forms/CustomButton.vue";
import { Link } from "@inertiajs/inertia-vue3";

const axios = inject("axios");

const { initSidebar } = useSidebarStore();

const form = ref({
    email: "",
    password: "",
});

const formValidation = ref({
    email: "",
    password: "",
});

const errorMessage = ref("");
const loginStatus = ref("");

function clearErrorMessage() {
    errorMessage.value = "";
}

async function login() {
    if (!validate()) return;

    loginStatus.value = "loading";

    try {
        const response = await axios.post("/api/auth/login", form.value);
        localStorage.setItem("access_token", response.data.result.access_token);

        window.location = route("home");
    } catch (error) {
        console.log(error);
        errorMessage.value =
            error.response?.data?.meta?.message ?? "Terjadi kesalahan";
        loginStatus.value = "error";
    }
}

function validate() {
    let result = true;

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
    }

    return result;
}

watch(
    () => form.value.email,
    (newValue, oldValue) => {
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
        if (newValue && newValue.length > 0) {
            formValidation.value.password = "";
        }
    }
);
</script>

<template>
    <DefaultAuthCard title="Masuk" :centerContent="true" :centerTitle="true">
        <form action="#">
            <AlertWarning
                v-if="errorMessage"
                @close="clearErrorMessage"
                :description="errorMessage"
                class="mb-6"
            />

            <InputGroup
                v-model="form.email"
                id="email"
                label="Email"
                type="text"
                placeholder="Masukkan Email"
                customClasses="mb-4.5"
                :warning="formValidation.email"
            />

            <InputGroup
                @enter="login"
                v-model="form.password"
                id="password"
                label="Password"
                type="password"
                placeholder="Masukan Kata Sandi"
                :warning="formValidation.password"
            />

            <CustomButton
                @click="login"
                :loading="loginStatus === 'loading'"
                :enable="true"
                color="bg-primary"
                class="mt-6"
            >
                Masuk
            </CustomButton>

            <div class="mt-6 text-center">
                <p class="text-sm">
                    Belum punya Akun?
                    <Link
                        :href="route('register')"
                        class="text-primary hover:font-bold"
                    >
                        Daftar
                    </Link>
                </p>
            </div>
        </form>
    </DefaultAuthCard>
</template>
