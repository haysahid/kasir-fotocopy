<script setup>
import { ref } from "vue";

const props = defineProps([
  "id",
  "label",
  "placeholder",
  "modelValue",
  "options",
  "warning",
]);
const emit = defineEmits(["update:modelValue"]);

const isOptionSelected = ref(false);

function updateValue(event) {
  isOptionSelected.value = true;
  emit("update:modelValue", event.target.value);
}
</script>

<template>
  <div class="w-full mb-4">
    <label :for="props.id" class="mb-2.5 block text-black dark:text-white">
      {{ props.label }}
    </label>

    <div class="relative z-20 bg-transparent dark:bg-form-input">
      <select
        :value="props.modelValue"
        :id="props.id"
        class="relative z-20 w-full py-4 pl-6 transition bg-transparent border rounded outline-none appearance-none pr-14 dark:text-white border-stroke focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary"
        :class="{
          '!border-danger focus:border-danger dark:!border-danger dark:focus:border-danger':
            props.warning,
          'text-black dark:text-white': isOptionSelected,
        }"
        @change="updateValue"
      >
        <option value="" disabled selected>{{ props.placeholder }}</option>

        <option v-for="option in props.options" :value="option.value">
          {{ option.text }}
        </option>
      </select>

      <span class="absolute z-10 -translate-y-1/2 top-1/2 right-4">
        <svg
          width="24"
          height="24"
          viewBox="0 0 24 24"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <g opacity="0.8">
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M5.29289 8.29289C5.68342 7.90237 6.31658 7.90237 6.70711 8.29289L12 13.5858L17.2929 8.29289C17.6834 7.90237 18.3166 7.90237 18.7071 8.29289C19.0976 8.68342 19.0976 9.31658 18.7071 9.70711L12.7071 15.7071C12.3166 16.0976 11.6834 16.0976 11.2929 15.7071L5.29289 9.70711C4.90237 9.31658 4.90237 8.68342 5.29289 8.29289Z"
              fill="#637381"
            ></path>
          </g>
        </svg>
      </span>
    </div>

    <p v-if="props.warning" class="mt-1 text-sm text-danger">
      {{ props.warning }}
    </p>
  </div>
</template>
