<script setup>
import { ref, onUpdated, inject } from "vue";
import CircularLoading from "../CircularLoading.vue";

const getReadableFileSize = inject("getReadableFileSize");

const props = defineProps([
  "id",
  "label",
  "type",
  "placeholder",
  "modelValue",
  "warning",
  "disabled",
  "currentFile",
  "uploadStatus",
  "uploadProgress",
]);
const emit = defineEmits(["update:modelValue"]);

const selectedFile = ref(null);

function updateValue(event) {
  selectedFile.value = event.target.files[0];
  emit("update:modelValue", selectedFile.value);
}

onUpdated(() => {
  if (props.currentFile) {
    selectedFile.value = props.currentFile;
  } else if (!props.modelValue) {
    selectedFile.value = null;
  }
});
</script>

<template>
  <div class="mb-4">
    <label
      :for="props.id"
      class="mb-2.5 block font-normal text-black dark:text-gray-200"
      >{{ props.label }}</label
    >

    <!-- File Preview -->
    <div
      v-if="selectedFile"
      class="flex flex-col gap-2 px-2 py-4 pl-6 pr-10 border rounded-lg outline-none sm:flex-row border-stroke dark:border-form-strokedark dark:bg-form-input"
    >
      <div class="flex items-center w-full">
        <div class="mt-0.5">
          <svg
            v-if="props.uploadStatus === 'success'"
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
              props.uploadStatus === 'loading' &&
              (props.uploadProgress === 0 || props.uploadProgress === 100)
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
            class="size-10 text-slate-500 dark:text-slate-400"
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
            {{ selectedFile.name }}
          </p>

          <!-- Progress Bar -->
          <div v-if="props.uploadStatus === 'loading'" class="w-full mt-2 mb-1">
            <div
              class="relative w-full h-1 rounded-full bg-stroke dark:bg-strokedark"
            >
              <div
                class="absolute top-0 h-1 transition-all duration-300 rounded-full bg-primary animate-pulse"
                :style="{ width: `${props.uploadProgress}%` }"
              ></div>
            </div>
          </div>

          <div class="flex justify-between mt-0.5">
            <p class="text-sm" v-if="selectedFile.size">
              {{ getReadableFileSize(selectedFile.size) }}
            </p>
            <p v-if="props.uploadStatus === 'loading'" class="text-sm">
              {{ props.uploadProgress }}%
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- File Upload Section -->
    <div
      v-else
      id="FileUpload"
      class="relative block w-full cursor-pointer appearance-none rounded border border-dashed border-primary dark:border-secondary dark:hover:border-primary bg-gray py-4 px-4 dark:bg-meta-4 sm:py-7.5 group"
      :class="{
        '!border-danger focus:border-danger dark:!border-danger dark:focus:border-danger':
          props.warning,
        'bg-whiten dark:bg-slate-700': props.disabled,
      }"
    >
      <input
        type="file"
        accept="*/*"
        class="absolute inset-0 z-50 w-full h-full p-0 m-0 outline-none opacity-0 cursor-pointer"
        @change="updateValue"
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
        <p class="text-sm font-medium text-body">
          Click to upload or drag and drop
        </p>
        <!-- <p class="mt-1.5 text-sm text-body font-medium">
          XLSX, DOCX, DOC or PDF
        </p> -->
      </div>
    </div>

    <p v-if="props.warning" class="mt-1 text-sm text-danger">
      {{ props.warning }}
    </p>
  </div>
</template>
