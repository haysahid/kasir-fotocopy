<script setup>
import { ref, inject, onUpdated, computed, watch } from "vue";
import InputGroup from "@/components/Forms/InputGroup.vue";
import AlertWarning from "../Alerts/AlertWarning.vue";
import CustomButton from "../Forms/CustomButton.vue";
import DefaultCard from "../Forms/DefaultCard.vue";

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

const form = ref({
  username: "",
  password: "",
  passwordConfirmation: "",
  realname: "",
  email: "",
  nohp: "",
  roleid: null,
  clientid: 1,
  isenabled: false,
});

const formValidation = ref({
  username: "",
  password: "",
  passwordConfirmation: "",
  realname: "",
  email: "",
  nohp: "",
  roleid: "",
  clientid: "",
});

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

    const response = await axios.put("/V1/api/v1/profile", form.value, {
      headers: { Authorization: token },
    });

    Toast.fire({
      icon: "success",
      title: response.data.message,
    });

    close(true);
    saveStatus.value = "success";
  } catch (error) {
    Toast.fire({
      icon: "warning",
      title: error.response?.data?.message || "Terjadi kesalahan",
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

  if (form.value.username.length < 1) {
    formValidation.value.username = "Username tidak boleh kosong!";
    result = false;
  } else if (form.value.username.includes(" ")) {
    formValidation.value.username = "Username cannot use space characters!";
    result = false;
  }

  // if (form.value.password.length < 1) {
  //   formValidation.value.password = "Password tidak boleh kosong!";
  //   result = false;
  // }

  // if (form.value.passwordConfirmation.length < 1) {
  //   formValidation.value.passwordConfirmation =
  //     "Password confirmation tidak boleh kosong!";
  //   result = false;
  // }

  if (form.value.realname.length < 1) {
    formValidation.value.realname = "Realname tidak boleh kosong!";
    result = false;
  }

  if (form.value.email.length < 1) {
    formValidation.value.email = "Email tidak boleh kosong!";
    result = false;
  } else if (
    !form.value.email.match(/\S+@\S+\.\S+/) ||
    form.value.email.includes(" ")
  ) {
    formValidation.value.email = "Please enter a valid email address!";
  }

  return result;
}

const disablePasswordConfirmation = computed(() =>
  form.value.password ? form.value.password.length < 1 : true
);

watch(
  () => form.value.username,
  (newValue, oldValue) => {
    if (newValue && newValue.length > 0 && formValidation.value.username) {
      formValidation.value.username = "";
    }

    if (newValue && newValue.length > 0 && newValue.includes(" ")) {
      formValidation.value.username = "Username cannot use space characters!";
    } else {
      formValidation.value.username = "";
    }
  }
);

watch(
  () => form.value.password,
  (newValue, oldValue) => {
    if (newValue && newValue.length > 0 && formValidation.value.password) {
      formValidation.value.password = "";
    }

    if (
      newValue &&
      newValue.length > 0 &&
      form.value.passwordConfirmation.length > 0 &&
      newValue !== form.value.passwordConfirmation
    ) {
      formValidation.value.passwordConfirmation =
        "Password confirmation does not match!";
    } else {
      formValidation.value.passwordConfirmation = "";
    }

    if (!newValue) {
      form.value.passwordConfirmation = "";
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
        "Password confirmation does not match!";
    } else {
      formValidation.value.passwordConfirmation = "";
    }
  }
);

watch(
  () => form.value.realname,
  (newValue, oldValue) => {
    if (newValue && newValue.length > 0 && formValidation.value.realname) {
      formValidation.value.realname = "";
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
      formValidation.value.email = "Please enter a valid email address!";
    } else {
      formValidation.value.email = "";
    }
  }
);

watch(
  () => form.value.nohp,
  (newValue, oldValue) => {
    if (newValue && newValue.length > 0 && formValidation.value.nohp) {
      formValidation.value.nohp = "";
    }
  }
);

onUpdated(() => {
  if (props.user) {
    form.value.username = props.user.username;
    form.value.password = props.user.password;
    form.value.passwordConfirmation = props.user.passwordConfirmation;
    form.value.realname = props.user.realname;
    form.value.email = props.user.email;
    form.value.nohp = props.user.nohp;
    form.value.roleid = props.user.roleid;
    form.value.clientid = props.user.clientid;
    form.value.isenabled = props.user.isenabled;
  }
});

function close(value) {
  if (props.autoClearData) {
    form.value = {
      username: "",
      password: "",
      passwordConfirmation: "",
      realname: "",
      email: "",
      nohp: "",
      roleid: null,
      clientid: 1,
      isenabled: false,
    };
  } else {
    form.value.username = props.user.username;
    form.value.password = props.user.password;
    form.value.passwordConfirmation = props.user.passwordConfirmation;
    form.value.realname = props.user.realname;
    form.value.email = props.user.email;
    form.value.nohp = props.user.nohp;
    form.value.roleid = props.user.roleid;
    form.value.clientid = props.user.clientid;
    form.value.isenabled = props.user.isenabled;
  }

  emit("close", value);
  saveStatus.value = "";
  errorMessage.value = "";
}
</script>

<template>
  <DefaultCard
    card-title="Update Profile"
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

      <InputGroup
        v-model="form.username"
        id="username"
        label="Username"
        type="text"
        placeholder="Enter username"
        customClasses="mb-4.5"
        :warning="formValidation.username"
        :disabled="true"
      />

      <InputGroup
        v-model="form.password"
        id="password"
        label="Password"
        type="password"
        placeholder="Enter password"
        :warning="formValidation.password"
      />

      <InputGroup
        v-show="!disablePasswordConfirmation"
        v-model="form.passwordConfirmation"
        id="passwordConfirmation"
        label="Re-type Password"
        type="password"
        placeholder="Re-enter password"
        :warning="formValidation.passwordConfirmation"
        :disabled="disablePasswordConfirmation"
      />

      <InputGroup
        v-model="form.realname"
        id="realname"
        label="Real Name"
        type="text"
        placeholder="Enter real name"
        customClasses="mb-4.5"
        :warning="formValidation.realname"
      />

      <InputGroup
        @enter="updateProfile"
        v-model="form.email"
        id="email"
        label="Email Address"
        type="email"
        placeholder="Enter email address"
        customClasses="mb-4.5"
        :warning="formValidation.email"
      />

      <InputGroup
        @enter="updateProfile"
        v-model="form.nohp"
        id="nohp"
        label="Phone Number"
        type="text"
        placeholder="Enter phone number"
        customClasses="mb-4.5"
        :warning="formValidation.nohp"
      />

      <div class="flex flex-col-reverse gap-x-4 sm:flex-row">
        <CustomButton
          @click="close(false)"
          :enable="saveStatus !== 'loading'"
          color="bg-danger"
          :is-full="true"
          padding="p-3"
          margin="mt-4"
        >
          Cancel
        </CustomButton>

        <CustomButton
          @click="updateProfile"
          :loading="saveStatus === 'loading'"
          color="bg-primary"
          :is-full="true"
          padding="p-3"
          margin="mt-4"
        >
          Save
        </CustomButton>
      </div>
    </div>
  </DefaultCard>
</template>
