<script setup>
import { ref, inject, onUpdated, computed, watch, onMounted } from "vue";
import InputGroup from "@/components/Forms/InputGroup.vue";
import AlertWarning from "../Alerts/AlertWarning.vue";
import CustomButton from "../Forms/CustomButton.vue";
import DefaultCard from "../Forms/DefaultCard.vue";
import { useConfigStore } from "@/stores/config";
import InputImageGroup from "@/components/Forms/InputImageGroup.vue";

const props = defineProps({
    showCloseButton: {
        type: Boolean,
    },
    user: {
        type: Object,
    },
    autoClearData: {
        type: Boolean,
        default: true,
    },
});
const emit = defineEmits(["close"]);

const axios = inject("axios");
const Toast = inject("Toast");

const configStore = useConfigStore();

const form = ref({
    name: "",
    email: "",
    password: "",
    passwordConfirmation: "",
    phone: "",
    address: "",
    avatar: "",
});

const formValidation = ref({
    name: "",
    email: "",
    password: "",
    passwordConfirmation: "",
    phone: "",
    address: "",
    avatar: "",
});

const avatarField = ref(null);

const errorMessage = ref("");
const saveStatus = ref("");

function clearErrorMessage() {
    errorMessage.value = "";
}

async function updateProfile() {
    if (!validateUpdateUser()) return;

    saveStatus.value = "loading";

    try {
        const token = `Bearer ${localStorage.getItem("access_token")}`;
        let formData = new FormData();

        for (const key in form.value) {
            if (key === "password" || key === "passwordConfirmation") {
                if (
                    formData[key] === null ||
                    formData[key] === undefined ||
                    formData[key] === ""
                ) {
                    continue;
                }
            }

            if (key === "avatar") {
                // Check avatar type is string or File
                if (typeof form.value[key] === "string") {
                    continue;
                }

                formData.append("avatar", form.value[key]);
                continue;
            }

            if (form.value[key] === null) {
                continue;
            }

            formData.append(key, form.value[key]);
        }

        const response = await axios.post("/api/profile", formData, {
            headers: { Authorization: token },
        });

        Toast.fire({
            icon: "success",
            title: response.data.meta.message,
        });

        close(true);
        saveStatus.value = "success";
    } catch (error) {
        Toast.fire({
            icon: "warning",
            title: error.response?.data?.meta?.message || "Terjadi kesalahan",
        });

        console.error(error);
        // close(false);
        saveStatus.value = "error";
        errorMessage.value = "Terjadi kesalahan";

        document
            .getElementById("updateProfileDialog")
            ?.scrollIntoView({ block: "start", behavior: "smooth" });
    }
}

function validateUpdateUser() {
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

    // if (form.value.password.length < 1) {
    //     formValidation.value.password = "Password tidak boleh kosong";
    //     result = false;
    // } else if (form.value.password.length < 8) {
    //     formValidation.value.password = "Password minimal 8 karakter";
    //     result = false;
    // }

    // if (form.value.passwordConfirmation.length < 1) {
    //     formValidation.value.passwordConfirmation =
    //         "Konfirmasi password tidak boleh kosong";
    //     result = false;
    // }

    if (form.value.phone.length < 1) {
        formValidation.value.phone = "Nomor HP tidak boleh kosong";
        result = false;
    } else if (!form.value.phone.match(/^\d+$/)) {
        formValidation.value.phone = "Nomor HP tidak valid";
        result = false;
    }

    return result;
}

const disablePasswordConfirmation = computed(() =>
    form.value.password ? form.value.password.length < 1 : true
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

// watch(
//     () => form.value.password,
//     (newValue, oldValue) => {
//         if (form.value.password.length > 0 && form.value.password.length < 8) {
//             formValidation.value.password = "Password minimal 8 karakter";
//         } else if (
//             newValue &&
//             newValue.length > 0 &&
//             formValidation.value.password
//         ) {
//             formValidation.value.password = "";
//         } else {
//             formValidation.value.password = "";
//         }

//         if (
//             newValue &&
//             newValue.length > 0 &&
//             form.value.passwordConfirmation.length > 0 &&
//             newValue !== form.value.passwordConfirmation
//         ) {
//             formValidation.value.passwordConfirmation =
//                 "Konfirmasi password tidak sama";
//         } else {
//             formValidation.value.passwordConfirmation = "";
//         }
//     }
// );

// watch(
//     () => form.value.passwordConfirmation,
//     (newValue, oldValue) => {
//         if (
//             newValue &&
//             newValue.length > 0 &&
//             form.value.password.length > 0 &&
//             newValue !== form.value.password
//         ) {
//             formValidation.value.passwordConfirmation =
//                 "Konfirmasi password tidak sama";
//         } else {
//             formValidation.value.passwordConfirmation = "";
//         }
//     }
// );

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

function getForm() {
    form.value = {
        name: props.user.name,
        email: props.user.email,
        password: "",
        passwordConfirmation: "",
        phone: props.user.phone,
        address: props.user.address,
    };

    form.value.avatar = props.user.avatar
        ? configStore.getImageUrl(props.user.avatar)
        : "";

    avatarField.value?.clearImage();
}

function clearForm() {
    form.value = {
        name: "",
        email: "",
        password: "",
        passwordConfirmation: "",
        phone: "",
        address: "",
        avatar: "",
    };

    formValidation.value = {
        name: "",
        email: "",
        password: "",
        passwordConfirmation: "",
        phone: "",
        address: "",
        avatar: "",
    };

    avatarField.value?.clearImage();
}

onUpdated(() => {
    if (props.user) {
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
    saveStatus.value = "";
    errorMessage.value = "";
}
</script>

<template>
    <DefaultCard
        card-title="Ubah Profil"
        :show-close-button="props.showCloseButton"
        @close="close(false)"
    >
        <div class="p-6.5">
            <AlertWarning
                v-if="errorMessage"
                @close="clearErrorMessage"
                :description="errorMessage"
                class="mb-6"
            />

            <InputImageGroup
                ref="avatarField"
                v-model="form.avatar"
                id="avatar"
                label="Foto Profil"
                type="file"
                placeholder="Pilih foto profil"
                :warning="formValidation.avatar"
                class="mb-4"
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

            <!-- <InputGroup
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
            /> -->

            <InputGroup
                @enter="updateProfile"
                v-model="form.phone"
                id="phone"
                label="Nomor HP"
                type="text"
                placeholder="Masukkan Nomor HP"
                :warning="formValidation.phone"
            />

            <InputGroup
                @enter="updateProfile"
                v-model="form.address"
                id="address"
                label="Alamat"
                type="text"
                placeholder="Masukkan Alamat"
                :warning="formValidation.address"
            />

            <div class="flex flex-col-reverse mt-6 gap-x-4 sm:flex-row">
                <CustomButton
                    @click="close(false)"
                    :enable="saveStatus !== 'loading'"
                    color="bg-danger"
                    :is-full="true"
                    padding="p-3"
                    margin="mt-4"
                >
                    Batal
                </CustomButton>

                <CustomButton
                    @click="updateProfile"
                    :loading="saveStatus === 'loading'"
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
