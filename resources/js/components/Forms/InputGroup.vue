<script setup>
import { ref, onMounted, onUpdated } from "vue";

const props = defineProps([
    "id",
    "label",
    "type",
    "placeholder",
    "modelValue",
    "warning",
    "disabled",
    "autoFocus",
]);
const emit = defineEmits(["update:modelValue", "enter"]);

const obscure = ref(props.type === "password");

function updateValue(event) {
    emit("update:modelValue", event.target.value);
}

function enter() {
    emit("enter");
}

// onMounted(() => {
//     if (props.autoFocus) {
//         document.getElementById(props.id).focus();
//     }
// });

// onMounted(() => {
//     if (props.autoFocus) {
//         document.getElementById(props.id).focus();
//     }
// });
</script>

<template>
    <div class="w-full mb-4">
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
                :type="props.type"
                :placeholder="props.placeholder"
                :id="props.id"
                class="w-full py-2 pl-3 pr-4 text-black duration-300 ease-linear bg-transparent border rounded-lg outline-none border-stroke focus:border-primary focus-visible:shadow-none dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary dark:text-white placeholder:text-gray-400"
                :class="{
                    '!border-danger focus:!border-danger dark:!border-danger dark:focus:!border-danger':
                        props.warning,
                    'bg-whiten dark:bg-slate-700': props.disabled,
                }"
                :disabled="props.disabled ?? false"
            />

            <span class="absolute top-[7px] right-2.5">
                <button
                    v-if="props.type === 'password'"
                    @click="obscure = !obscure"
                    type="button"
                    class="p-1"
                >
                    <svg
                        v-if="obscure"
                        class="flex-shrink-0 text-gray-400 size-5 dark:hover:text-secondary hover:text-primary dark:text-neutral-500"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                        <path
                            d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"
                        ></path>
                        <path
                            d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"
                        ></path>
                        <line x1="2" x2="22" y1="2" y2="22"></line>
                    </svg>
                    <svg
                        v-else
                        class="flex-shrink-0 text-gray-400 size-5 dark:hover:text-secondary hover:text-primary dark:text-neutral-500"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path
                            d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"
                        ></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
                <slot></slot>
            </span>
        </div>

        <p v-if="props.warning" class="mt-1 text-xs text-danger">
            {{ props.warning }}
        </p>
    </div>
</template>
