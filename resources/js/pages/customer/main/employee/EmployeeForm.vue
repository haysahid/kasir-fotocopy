<script setup>
import { ref, onMounted, onUpdated, computed, watch } from "vue";
import InputGroup from "@/components/Forms/InputGroup.vue";
import AlertWarning from "@/components/Alerts/AlertWarning.vue";
import CustomButton from "@/components/Forms/CustomButton.vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import CustomSwitch from "@/components/Forms/CustomSwitch.vue";
import { useEmployeeStore } from "@/stores/employee";
import { useRoleStore } from "@/stores/admin/role";
import SearchSelectGroup from "@/components/Forms/SearchSelectGroup.vue";

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

const employeeStore = useEmployeeStore();

const roleStore = useRoleStore();
const roleOptions = ref([]);

async function getRoleDropdown() {
    await roleStore.getDropdown();

    roleOptions.value = roleStore.dropdown.map((role) => ({
        value: role.id,
        text: role.name,
    }));
}

function filterRoleOptions(search = null) {
    if (!roleStore.dropdown) return;

    console.log("search", search);

    const options = roleStore.dropdown.map((role) => ({
        value: role.id,
        text: role.name,
    }));

    if (search) {
        roleOptions.value = options.filter((option) =>
            option.text.toLowerCase().includes(search.toLowerCase())
        );

        return;
    }

    roleOptions.value = options;
}

const form = ref({
    name: "",
    email: "",
    password: "",
    passwordConfirmation: "",
    phone: "",
    address: "",
    role_id: "",
});

const formValidation = ref({
    name: "",
    email: "",
    password: "",
    passwordConfirmation: "",
    phone: "",
    address: "",
    role_id: "",
});

function clearErrorMessage() {
    employeeStore.errorMessage = "";
}

async function saveItem() {
    if (props.user) {
        await updateItem();
    } else {
        await addItem();
    }
}

async function addItem() {
    if (!validateAddItem()) return;

    const data = form.value;

    const response = await employeeStore.addItem(data);

    if (response && response.meta.code === 201) {
        close(true);
    } else {
        document
            .getElementById("pagetop")
            ?.scrollIntoView({ block: "start", behavior: "smooth" });
    }
}

async function updateItem() {
    if (!validateUpdateItem()) return;

    const updatedData = form.value;

    for (const key in updatedData) {
        if (key === "password" || key === "passwordConfirmation") {
            if (
                updatedData[key] === null ||
                updatedData[key] === undefined ||
                updatedData[key] === ""
            ) {
                delete updatedData[key];
            }
        }
    }

    const response = await employeeStore.updateItem(props.user.id, updatedData);

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

    if (form.value.password && form.value.passwordConfirmation.length < 1) {
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

function validateUpdateItem() {
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

    if (form.value.password && form.value.password.length < 1) {
        formValidation.value.password = "Password tidak boleh kosong";
        result = false;
    } else if (form.value.password && form.value.password.length < 8) {
        formValidation.value.password = "Password minimal 8 karakter";
        result = false;
    }

    if (form.value.password && form.value.passwordConfirmation.length < 1) {
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

watch(
    () => form.value.password,
    (newValue, oldValue) => {
        if (!form.value.password) return;

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
        if (!form.value.password) return;

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

watch(
    () => form.value.role_id,
    (newValue, oldValue) => {
        if (newValue && newValue.length > 0 && formValidation.value.role_id) {
            formValidation.value.role_id = "";
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
        role_id: props.user.role_id,
    };
}

function clearForm() {
    form.value = {
        name: "",
        email: "",
        password: "",
        passwordConfirmation: "",
        phone: "",
        address: "",
        role_id: "",
    };

    formValidation.value = {
        name: "",
        email: "",
        password: "",
        passwordConfirmation: "",
        phone: "",
        address: "",
        role_id: "",
    };
}

onUpdated(() => {
    if (props.user) {
        getForm();
    }
});

onMounted(() => {
    getRoleDropdown();
});

function close(value) {
    if (props.autoClearData) {
        clearForm();
    } else {
        getForm();
    }

    emit("close", value);
    employeeStore.saveStatus = "";
    employeeStore.errorMessage = "";
}
</script>

<template>
    <DefaultCard
        :card-title="props.user ? 'Ubah Karyawan' : 'Tambah Karyawan'"
        :show-close-button="props.showCloseButton"
        @close="close(false)"
    >
        <div class="p-6.5">
            <AlertWarning
                v-if="employeeStore.errorMessage"
                @close="clearErrorMessage"
                :description="employeeStore.errorMessage"
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
                @enter="updateItem"
                v-model="form.phone"
                id="phone"
                label="Nomor HP"
                type="text"
                placeholder="Masukkan Nomor HP"
                :warning="formValidation.phone"
            />

            <InputGroup
                @enter="updateItem"
                v-model="form.address"
                id="address"
                label="Alamat"
                type="text"
                placeholder="Masukkan Alamat"
                :warning="formValidation.address"
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

            <div class="flex flex-col-reverse mt-6 gap-x-4 sm:flex-row">
                <CustomButton
                    @click="close(false)"
                    :enable="employeeStore.saveStatus !== 'loading'"
                    color="bg-danger"
                    :is-full="true"
                    padding="p-3"
                    margin="mt-4"
                >
                    Batal
                </CustomButton>

                <CustomButton
                    @click="saveItem"
                    :loading="employeeStore.saveStatus === 'loading'"
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
