<script setup>
import { inject, ref, onMounted, watch } from "vue";
import { useRouter } from "vue-router";
import DefaultCard from "./DefaultCard.vue";
import SearchSelect from "@/components/Forms/SearchSelect.vue";
import InputGroup from "@/components/Forms/InputGroup.vue";
import SelectGroup from "./SelectGroup.vue";
import CustomButton from "./CustomButton.vue";
import PrintView from "@/views/PrintView.vue";


const axios = inject("axios");
const Toast = inject("Toast");
const getReadableFileSize = inject("getReadableFileSize");

const router = useRouter();

const yearangOptions = ref([]);
const tahapandetOptions = ref([]);

const paper = ref(null);
const logo = ref(null);

const form = ref({
  file: null,
  yearangid: null,
  yearang: {},
  tahapandetid: null,
  tahapandet: {},
});

const formValidation = ref({
  yearangid: "",
  tahapandetid: "",
});

const invoice = ref(null);

const generateStatus = ref("");
const generateProgress = ref(0);
const errorMessage = ref("");

const uploadController = ref(new AbortController());

function clearErrorMessage() {
  errorMessage.value = "";
}

function onTahapandetSelect(value) {
  form.value.tahapandetid = value.ID;
  form.value.tahapandet = value;
  formValidation.value.tahapandetid = "";
}

function removeSelectedTahapandet() {
  form.value.tahapandetid = null;
  form.value.tahapandet = {};
}

async function getYearangs(search = "") {
  const params = {
    limit: null,
    page: 1,
    search: search,
  };

  try {
    const token = `Bearer ${localStorage.getItem("access_token")}`;
    const response = await axios.get("/V1/api/v1/yearang", {
      headers: { Authorization: token },
      params: params,
    });
    yearangOptions.value = response.data.data.rows;
  } catch (error) {
    console.error(error);
  }
}

async function getTahapandets(search = "") {
  const params = {
    limit: null,
    page: 1,
    search: search,
  };

  try {
    const token = `Bearer ${localStorage.getItem("access_token")}`;
    const response = await axios.get(
      `/V1/api/v1/tahapandetcomboapbd2/${form.value.yearangid}`,
      {
        headers: { Authorization: token },
        params: params,
      }
    );
    tahapandetOptions.value = response.data.data;
  } catch (error) {
    console.error(error);
  }
}

async function generateInvoice() {
  if (!validateTahapanDet()) {
    return Toast.fire({
      icon: "warning",
      title: "Please fill in all required fields correctly!",
    });
  }

  generateStatus.value = "loading";

  try {
    const token = `Bearer ${localStorage.getItem("access_token")}`;

    uploadController.value = new AbortController();

    const response = await axios.get(
      `/V1/api/v1/report/lampiran1/${form.value.tahapandetid}`,
      {
        signal: uploadController.value.signal,
        headers: {
          Authorization: token,
        },
      }
    );

    Toast.fire({
      icon: "success",
      title: response.data.message,
    });

    invoice.value = response.data.data;

    generateStatus.value = "success";
  } catch (error) {
    if (error.code === "ERR_CANCELED") return;

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
    generateStatus.value = "error";
  }

  generateProgress.value = 0;
}

watch(
  () => form.value.yearangid,
  (newValue, oldValue) => {
    if (newValue && newValue !== oldValue) {
      form.value.yearang = yearangOptions.value.find(
        (option) => option.ID === form.value.yearangid
      );

      form.value.tahapandetid = null;
      form.value.tahapandet = {};

      getTahapandets();
    }

    if (newValue && formValidation.value.yearangid) {
      formValidation.value.yearangid = "";
    }
  }
);

watch(
  () => form.value.tahapandetid,
  (newValue, oldValue) => {
    if (newValue && newValue.length > 0 && formValidation.value.tahapandetid) {
      formValidation.value.tahapandetid = "";
    }
  }
);

const cancelGenerateInvoice = () => {
  uploadController.value.abort();
  generateProgress.value = 0;
  generateStatus.value = "error";
};

onMounted(() => {
  getYearangs();
  paper.value = document.getElementById("paper");
});

function validateTahapanDet() {
  let result = true;

  if (!form.value.yearangid) {
    formValidation.value.yearangid = "Tahun anggaran harus dipilih!";
    result = false;
  }

  if (!form.value.tahapandetid) {
    formValidation.value.tahapandetid = "Tahapan APBD harus dipilih!";
    result = false;
  }

  return result;
}

function printInvoice() {
  const contents = paper.value.innerHTML;

  var a = window.open("", "", "height=500, width=500");
  a.document.write("<html>");
  a.document.write(
    `<head>
      <link rel="stylesheet" href="/src/assets/css/style.css" onload="print()" />
      <link rel="stylesheet" href="/src/assets/css/printkusipd.css" />
      <style>
        @page {
          size: A4;
          margin-top: 2.5rem;
          margin-bottom: 2.5rem;
          margin-left: 2.5rem;
          margin-right: 2.5rem;
        }

        @media print {
          #page-number::after {
            counter-increment: page !important;
            content: "Halaman " counter(page);
          }
        }
      </style>
    </head>`
  );
  a.document.write("<body>");
  a.document.write(contents);
  a.document.write("</body></head>");
}

</script>

<template>
  <DefaultCard card-title="Laporan Ringkasan APBD">
    <div class="p-7">
      <form @submit.prevent="generateInvoice">
        <SelectGroup v-if="yearangOptions" v-model.number="form.yearangid" id="yearang" label="Tahun Anggaran"
          type="text" placeholder="Pilih tahun anggaran" :options="yearangOptions.map((option) => ({
            text: option.nama,
            value: option.ID,
          }))
            " :warning="formValidation.yearangid" />

        <InputGroup v-if="form.tahapandet?.nama" v-model="form.tahapandet.nama" id="tahapandet" label="Tahapan APBD"
          type="text" placeholder="Masukan nama tahapan detail" :disabled="true">
          <button type="button" class="p-2 hover:text-danger dark:text-bodydark2 dark:hover:text-danger"
            @click="removeSelectedTahapandet">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="size-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
          </button>
        </InputGroup>

        <SearchSelect v-else id="selectTahapandet" label="Pilih Tahapan" placeholder="Tahapan APBD"
          :options="tahapandetOptions" @search="getTahapandets" @select="onTahapandetSelect"
          :warning="formValidation.tahapandetid" />

        <CustomButton @click="generateInvoice" :loading="generateStatus === 'loading'" color="bg-success"
          :is-full="true" padding="p-3" margin="mt-4">
          Proses
        </CustomButton>

        <CustomButton v-if="generateStatus === 'success'" @click="printInvoice" :loading="generateStatus === 'loading'"
          color="bg-primary" :is-full="true" padding="p-3" margin="mt-4">
          Cetak
        </CustomButton>
      </form>

      <div id="paper" class="mt-4">
        <PrintView v-if="generateStatus === 'success'" :invoice="invoice" />
      </div>
    </div>
  </DefaultCard>
</template>
