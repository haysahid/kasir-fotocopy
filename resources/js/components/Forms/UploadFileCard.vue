<script setup>
import { inject, ref, onMounted, watch } from "vue";
import DefaultCard from "./DefaultCard.vue";
import CircularLoading from "../CircularLoading.vue";
import SearchSelect from "@/components/Forms/SearchSelect.vue";
import InputGroup from "@/components/Forms/InputGroup.vue";
import SelectGroup from "./SelectGroup.vue";
//import SelectGroup from "./SelectGroup.vue";

const axios = inject("axios");
const Toast = inject("Toast");
const getReadableFileSize = inject("getReadableFileSize");

const yearangOptions = ref([]);
const tahapandetOptions = ref([]);

const form = ref({
  file: null,
  yearangid: null,
  yearang: {},
  tahapandetid: null,
  tahapandet: {},
});

const formValidation = ref({
  tahapandetid: "",
});

const uploadStatus = ref("");
const uploadProgress = ref(0);
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

async function uploadFile(event) {
  if (!validateTahapanDet()) {
    return Toast.fire({
      icon: "warning",
      title: "Please fill in all required fields correctly!",
    });
  }

  uploadStatus.value = "loading";

  try {
    const token = `Bearer ${localStorage.getItem("access_token")}`;
    const formData = new FormData();
    formData.append("uploadFile", form.value.file);

    uploadController.value = new AbortController();

    const response = await axios.post(
      `/V1/api/v1/uploadrekapsipd/belanja/${form.value.yearangid}/${form.value.tahapandetid}`,
      formData,
      {
        signal: uploadController.value.signal,
        maxContentLength: Infinity,
        maxBodyLength: Infinity,
        headers: {
          Authorization: token,
          "Content-Type": "multipart/form-data",
        },
        onUploadProgress: (progressEvent) => {
          const progress = Math.round(
            (progressEvent.loaded / progressEvent.total) * 100
          );

          uploadProgress.value = progress;
        },
      }
    );

    Toast.fire({
      icon: "success",
      title: response.data.message,
    });

    uploadStatus.value = "success";
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
    uploadStatus.value = "error";
  }

  uploadProgress.value = 0;
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
  }
);

watch(
  () => form.value.tahapandetid,
  (newValue, oldValue) => {
    if (newValue && newValue !== oldValue) {
      form.value.file = null;
    }
  }
);

const cancelUpload = () => {
  uploadController.value.abort();
  uploadProgress.value = 0;
  uploadStatus.value = "error";
};

const handleFileChange = (event) => {
  form.value.file = event.target.files[0];
};

const resetFile = () => {
  form.value.file = null;
  uploadProgress.value = 0;
  uploadStatus.value = "";
};

onMounted(() => {
  getYearangs();
});

function validateTahapanDet() {
  let result = true;
  if (!form.value.tahapandetid) {
    formValidation.value.tahapandetid = "Tahapan APBD harus dipilih!";
    result = false;
  }

  return result;
}
</script>

<template>
  <DefaultCard card-title="Upload File Belanja">
    <div class="p-7">
      <form @submit.prevent="uploadFile">
        <SelectGroup
          v-if="yearangOptions"
          v-model.number="form.yearangid"
          id="yearang"
          label="Tahun Anggaran"
          type="text"
          placeholder="Pilih tahun anggaran"
          :options="
            yearangOptions.map((option) => ({
              text: option.nama,
              value: option.ID,
            }))
          "
        />

        <InputGroup
          v-if="form.tahapandet?.nama"
          v-model="form.tahapandet.nama"
          id="tahapandet"
          label="Tahapan APBD"
          type="text"
          placeholder="Masukan nama tahapan detail"
          :disabled="true"
        >
          <button
            type="button"
            class="p-2 hover:text-danger dark:text-bodydark2 dark:hover:text-danger"
            @click="removeSelectedTahapandet"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="size-5"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M6 18 18 6M6 6l12 12"
              />
            </svg>
          </button>
        </InputGroup>

        <SearchSelect
          v-else
          id="selectTahapandet"
          label="Pilih Tahapan"
          placeholder="Tahapan APBD"
          :options="tahapandetOptions"
          @search="getTahapandets"
          @select="onTahapandetSelect"
          :warning="formValidation.tahapandetid"
        />

        <!-- Select Status -->
        <!---
        <SelectGroup
          v-model.number="form.tahapanDet"
          id="tahapanDet"
          label="Tahapan APBD"
          placeholder="Pilih Tahapan"
          :options="[{ value: 1, text: 'Sembarang' }]"
        /> 
        --->

        <template v-if="form.tahapandetid">
          <!-- File Preview -->
          <div
            v-if="form.file"
            class="flex flex-col gap-2 px-2 mt-8 sm:flex-row"
          >
            <div class="flex w-full">
              <div class="mt-0.5">
                <svg
                  v-if="uploadStatus === 'success'"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  fill="currentColor"
                  class="size-10 text-success"
                >
                  <path
                    fill-rule="evenodd"
                    d="M9 1.5H5.625c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0 0 16.5 9h-1.875a1.875 1.875 0 0 1-1.875-1.875V5.25A3.75 3.75 0 0 0 9 1.5Zm6.61 10.936a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 14.47a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z"
                    clip-rule="evenodd"
                  />
                  <path
                    d="M12.971 1.816A5.23 5.23 0 0 1 14.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 0 1 3.434 1.279 9.768 9.768 0 0 0-6.963-6.963Z"
                  />
                </svg>

                <div
                  v-else-if="
                    uploadStatus === 'loading' &&
                    (uploadProgress === 0 || uploadProgress === 100)
                  "
                  class="flex items-center justify-center size-10"
                >
                  <CircularLoading class="p-1.5 bg-primary size-9" />
                </div>

                <svg
                  v-else
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  fill="currentColor"
                  class="size-10"
                >
                  <path
                    fill-rule="evenodd"
                    d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0 0 16.5 9h-1.875a1.875 1.875 0 0 1-1.875-1.875V5.25A3.75 3.75 0 0 0 9 1.5H5.625ZM7.5 15a.75.75 0 0 1 .75-.75h7.5a.75.75 0 0 1 0 1.5h-7.5A.75.75 0 0 1 7.5 15Zm.75 2.25a.75.75 0 0 0 0 1.5H12a.75.75 0 0 0 0-1.5H8.25Z"
                    clip-rule="evenodd"
                  />
                  <path
                    d="M12.971 1.816A5.23 5.23 0 0 1 14.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 0 1 3.434 1.279 9.768 9.768 0 0 0-6.963-6.963Z"
                  />
                </svg>
              </div>

              <div class="mx-2.5 w-full">
                <p class="font-medium text-black dark:text-gray-200 text-nowrap">
                  {{ form.file.name }}
                </p>

                <!-- Progress Bar -->
                <div v-if="uploadStatus === 'loading'" class="w-full mt-2 mb-1">
                  <div
                    class="relative w-full h-1 rounded-full bg-stroke dark:bg-strokedark"
                  >
                    <div
                      class="absolute top-0 h-1 transition-all duration-300 rounded-full bg-primary animate-pulse"
                      :style="{ width: `${uploadProgress}%` }"
                    ></div>
                  </div>
                </div>

                <div class="flex justify-between">
                  <p class="mt-0.5 text-sm">
                    {{ getReadableFileSize(form.file.size) }}
                  </p>
                  <p v-if="uploadStatus === 'loading'" class="mt-0.5 text-sm">
                    {{ uploadProgress }}%
                  </p>
                </div>
              </div>
            </div>

            <div class="flex items-center gap-4.5 mt-1">
              <button
                v-if="uploadStatus === 'success'"
                class="flex justify-center px-6 py-2 font-medium rounded bg-primary text-gray hover:bg-opacity-90"
                type="button"
                @click="resetFile"
              >
                Okay
              </button>

              <button
                v-else-if="uploadStatus === 'loading'"
                class="flex justify-center px-6 py-2 font-medium text-black border rounded border-stroke hover:shadow-1 dark:border-strokedark dark:bg-slate-600 dark:text-gray dark:hover:bg-opacity-90"
                type="button"
                @click="cancelUpload"
              >
                Cancel
              </button>

              <template v-else>
                <button
                  class="flex justify-center px-6 py-2 font-medium text-black border rounded border-stroke hover:shadow-1 dark:border-strokedark dark:bg-slate-600 dark:text-gray dark:hover:bg-opacity-90"
                  type="button"
                  @click="resetFile"
                >
                  Cancel
                </button>

                <button
                  class="flex justify-center px-6 py-2 font-medium rounded bg-primary text-gray hover:bg-opacity-90"
                  type="submit"
                >
                  Upload
                </button>
              </template>
            </div>
          </div>

          <!-- File Upload Section -->
          <div
            v-else
            id="FileUpload"
            class="relative mt-6 mb-5.5 block w-full cursor-pointer appearance-none rounded border border-dashed border-primary dark:border-secondary dark:hover:border-primary bg-gray py-4 px-4 dark:bg-meta-4 sm:py-7.5 group"
          >
            <input
              type="file"
              accept="*/*"
              class="absolute inset-0 z-50 w-full h-full p-0 m-0 outline-none opacity-0 cursor-pointer"
              @change="handleFileChange"
            />
            <div class="flex flex-col items-center justify-center space-y-3">
              <span
                class="flex items-center justify-center bg-white border rounded-full group-hover:bg-primary w-11 h-11 border-stroke dark:border-strokedark dark:bg-boxdark"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="2"
                  stroke="currentColor"
                  class="size-5 text-primary dark:text-secondary group-hover:text-gray-200"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"
                  />
                </svg>
              </span>
              <p class="text-sm font-medium">
                Click to upload or drag and drop
              </p>
              <p class="mt-1.5 text-sm font-medium">XLSX, DOCX, DOC or PDF</p>
            </div>
          </div>
        </template>
      </form>
    </div>
  </DefaultCard>
</template>
