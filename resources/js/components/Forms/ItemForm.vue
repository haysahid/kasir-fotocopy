<script setup>
import { ref, inject, onUpdated, watch } from "vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import InputGroup from "@/components/Forms/InputGroup.vue";
import CustomButton from "./CustomButton.vue";
import AlertWarning from "@/components/Alerts/AlertWarning.vue";
import { useRoute, useRouter } from "vue-router";

const props = defineProps({
  showCloseButton: Boolean,
  item: Object,
  autoClearData: {
    type: Boolean,
    default: true,
  },
});
const emit = defineEmits(["close"]);

const axios = inject("axios");
const Toast = inject("Toast");
const route = useRoute();
const router = useRouter();

const form = ref({
  name: "",
});
const saveStatus = ref("");
const inputNameValidation = ref("");
const errorMessage = ref("");

function clearErrorMessage() {
  errorMessage.value = "";
}

function saveItem() {
  if (!form.value.name || form.value.name.length < 1) {
    inputNameValidation.value = "Name tidak boleh kosong!";
    return;
  }

  saveStatus.value = "loading";

  if (props.item) {
    updateItem();
  } else {
    addItem();
  }
}

async function addItem() {
  try {
    const token = `Bearer ${localStorage.getItem("access_token")}`;
    const response = await axios.post(
      "/V1/api/v1/item",
      {
        nama: form.value.name,
      },
      {
        headers: { Authorization: token },
      }
    );

    Toast.fire({
      icon: "success",
      title: response.data.message,
    });

    close(true);
    saveStatus.value = "success";
  } catch (error) {
    if (
      error.response?.status === 400 &&
      error.response?.data === "Token is expired\n"
    ) {
      close(false);

      return Toast.fire({
        icon: "warning",
        title: "Sesi login telah berakhir, silahkan login kembali",
        didDestroy: () => {
          router.push({
            name: "login",
            query: { redirect: route.fullPath },
          });
        },
      });
    }

    Toast.fire({
      icon: "warning",
      title: error.response?.data?.message || "Terjadi kesalahan",
    });

    console.error(error);
    // close(false);
    saveStatus.value = "error";
    errorMessage.value = "Terjadi kesalahan";
  }
}

async function updateItem() {
  if (form.value.name == props.item.nama) {
    close(false);
    saveStatus.value = "success";
    return;
  }

  try {
    const token = `Bearer ${localStorage.getItem("access_token")}`;
    const response = await axios.put(
      `/V1/api/v1/item/${props.item.ID}`,
      {
        nama: form.value.name,
      },
      {
        headers: { Authorization: token },
      }
    );

    Toast.fire({
      icon: "success",
      title: response.data.message,
    });

    close(true);
    saveStatus.value = "success";
  } catch (error) {
    if (
      error.response?.status === 400 &&
      error.response?.data === "Token is expired\n"
    ) {
      close(false);

      return Toast.fire({
        icon: "warning",
        title: "Sesi login telah berakhir, silahkan login kembali",
        didDestroy: () => {
          router.push({
            name: "login",
            query: { redirect: route.fullPath },
          });
        },
      });
    }

    Toast.fire({
      icon: "warning",
      title: error.response?.data?.message || "Terjadi kesalahan",
    });

    console.error(error);
    // close(false);
    saveStatus.value = "error";
    errorMessage.value = "Terjadi kesalahan";
  }
}

function close(value) {
  if (props.autoClearData) {
    form.value.name = "";
  } else {
    form.value.name = props.item.nama;
  }

  saveStatus.value = "";
  inputNameValidation.value = "";
  errorMessage.value = "";
  emit("close", value);
}

onUpdated(() => {
  if (props.item) {
    form.value.name = props.item.nama;
  }
});

watch(
  () => form.value.name,
  (newValue, oldValue) => {
    if (newValue && newValue.length > 0 && inputNameValidation.value) {
      inputNameValidation.value = "";
    }
  }
);
</script>

<template>
  <DefaultCard
    :card-title="props.item ? 'Update Item' : 'Add Item'"
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
        v-model="form.name"
        @enter="saveItem"
        id="name"
        label="Name"
        type="text"
        placeholder="Enter name"
        customClasses="mb-4.5"
        :warning="inputNameValidation"
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
          @click="saveItem"
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
