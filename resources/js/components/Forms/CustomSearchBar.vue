<script setup>
import { ref } from "vue";

const props = defineProps([
    "id",
    "label",
    "type",
    "placeholder",
    "modelValue",
    "warning",
    "disabled",
    "useDebounce",
]);
const emit = defineEmits(["update:modelValue", "enter"]);

const debounceTimeout = ref(null);

function updateValue(event) {
    if (props.useDebounce) {
        window.clearTimeout(debounceTimeout.value);
        debounceTimeout.value = window.setTimeout(() => {
            emit("update:modelValue", event.target.value);
        }, 500);
    } else {
        emit("update:modelValue", event.target.value);
    }
}

function enter() {
    emit("enter");
}
</script>

<template>
    <div class="w-full min-w-[160]">
        <label
            v-if="props.label"
            :for="props.id"
            class="block mb-1.5 text-sm font-normal text-black dark:text-white"
        >
            {{ props.label }}
        </label>

        <div class="relative">
            <input
                @input="updateValue($event)"
                @keyup.enter="enter"
                :value="props.modelValue"
                type="text"
                :placeholder="props.placeholder"
                :id="props.id"
                class="w-full py-2 pl-3 pr-10 text-black duration-300 ease-linear bg-transparent border rounded-lg outline-none text-ellipsis border-stroke focus:border-primary focus-visible:shadow-none dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary dark:text-white placeholder:text-gray-400"
                :class="{
                    '!border-danger focus:!border-danger dark:!border-danger dark:focus:!border-danger':
                        props.warning,
                    'bg-whiten dark:bg-slate-700': props.disabled,
                }"
                :disabled="props.disabled ?? false"
            />

            <span class="absolute top-[7px] right-2.5">
                <div class="p-1">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="flex-shrink-0 text-gray-400 size-5 dark:text-neutral-500"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"
                        />
                    </svg>
                </div>
                <slot></slot>
            </span>
        </div>

        <p v-if="props.warning" class="mt-1 text-xs text-danger">
            {{ props.warning }}
        </p>
    </div>
</template>
