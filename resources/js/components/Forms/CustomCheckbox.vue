<script setup>
import { onUpdated, ref, watch } from "vue";

const props = defineProps([
  "id",
  "label",
  "type",
  "placeholder",
  "modelValue",
  "warning",
  "disabled",
]);
const emit = defineEmits(["update:modelValue"]);

const selected = ref(props.modelValue);

function updateValue(event) {
  emit("update:modelValue", selected.value);
}

onUpdated(() => {
  selected.value = props.modelValue;
});

watch(
  () => selected.value,
  (newValue, oldValue) => {
    updateValue();
  }
);
</script>

<template>
  <div class="relative pt-0.5 mb-4">
    <label
      :for="props.id"
      class="flex items-center cursor-pointer"
      :class="{
        'gap-x-3': props.label,
      }"
    >
      <input
        type="checkbox"
        :id="props.id"
        class="sr-only taskCheckbox"
        v-model="selected"
      />
      <div
        class="flex items-center justify-center w-5 h-5 border rounded box border-stroke dark:border-form-strokedark dark:bg-form-input"
      >
        <span class="text-gray-200 opacity-0">
          <svg
            class="fill-current"
            width="10"
            height="7"
            viewBox="0 0 10 7"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M9.70685 0.292804C9.89455 0.480344 10 0.734667 10 0.999847C10 1.26503 9.89455 1.51935 9.70685 1.70689L4.70059 6.7072C4.51283 6.89468 4.2582 7 3.9927 7C3.72721 7 3.47258 6.89468 3.28482 6.7072L0.281063 3.70701C0.0986771 3.5184 -0.00224342 3.26578 3.785e-05 3.00357C0.00231912 2.74136 0.10762 2.49053 0.29326 2.30511C0.4789 2.11969 0.730026 2.01451 0.992551 2.01224C1.25508 2.00996 1.50799 2.11076 1.69683 2.29293L3.9927 4.58607L8.29108 0.292804C8.47884 0.105322 8.73347 0 8.99896 0C9.26446 0 9.51908 0.105322 9.70685 0.292804Z"
              fill=""
            />
          </svg>
        </span>
      </div>
      <div v-if="props.label" class="dark:text-gray-200">{{ props.label }}</div>
    </label>
  </div>
</template>
