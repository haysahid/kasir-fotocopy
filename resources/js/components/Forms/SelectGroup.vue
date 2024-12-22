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
    <div class="w-full">
        <label
            v-if="props.label"
            :for="props.id"
            class="mb-1.5 block text-gray-900 dark:text-gray-200"
        >
            {{ props.label }}
        </label>

        <select
            :value="props.modelValue"
            :id="props.id"
            class="relative z-20 w-full py-2 pl-3 pr-4 transition duration-300 ease-linear bg-transparent border rounded-lg outline-none appearance-none dark:text-gray-200 border-stroke focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary placeholder:text-gray-400"
            :class="{
                '!border-danger focus:border-danger dark:!border-danger dark:focus:border-danger':
                    props.warning,
                'text-gray-900 dark:text-gray-200':
                    isOptionSelected && props.modelValue,
            }"
            @change="updateValue"
        >
            <option
                v-if="props.placeholder"
                value=""
                disabled
                selected
                class="disabled:text-gray-400"
            >
                {{ props.placeholder }}
            </option>

            <option v-for="option in props.options" :value="option.value">
                {{ option.text }}
            </option>
        </select>

        <p v-if="props.warning" class="mt-1 text-xs text-danger">
            {{ props.warning }}
        </p>
    </div>
</template>
