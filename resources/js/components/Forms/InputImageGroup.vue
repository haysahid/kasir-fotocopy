<script setup>
import { ref, onUpdated } from "vue";

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

const imagePath = ref(null);

var loadFile = function (event) {
    var input = event.target;
    var file = input.files[0];
    var type = file.type;

    imagePath.value = URL.createObjectURL(event.target.files[0]);

    emit("update:modelValue", file);
};

function clearImage() {
    imagePath.value = null;
}

onUpdated(() => {});

defineExpose({ clearImage });
</script>

<template>
    <div class="flex flex-col items-center w-full space-x-6">
        <label :for="props.id" class="block w-full cursor-pointer">
            <div class="shrink-0">
                <div
                    v-if="imagePath || props.modelValue"
                    class="relative w-full group"
                >
                    <img
                        id="preview_img"
                        class="object-cover w-full rounded-2xl aspect-square"
                        :src="imagePath || props.modelValue"
                        alt="Current image"
                    />
                    <div
                        class="absolute inset-0 flex flex-col items-center justify-center w-full h-full duration-300 ease-linear bg-black bg-opacity-0 rounded-2xl group-hover:bg-opacity-70"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="text-gray-400 text-opacity-0 duration-300 ease-linear dark:text-opacity-0 size-6 dark:text-gray-400 group-hover:text-gray-300 dark:group-hover:text-gray-300 group-hover:text-opacity-100 dark:group-hover:text-opacity-100"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"
                            />
                        </svg>

                        <span
                            class="text-gray-400 text-opacity-0 duration-300 ease-linear dark:text-opacity-0 dark:text-gray-400 group-hover:text-gray-300 dark:group-hover:text-gray-300 group-hover:text-opacity-100 dark:group-hover:text-opacity-100"
                        >
                            Ganti gambar
                        </span>
                    </div>
                </div>

                <div
                    v-else
                    class="flex items-center justify-center w-full h-32 text-gray-400 duration-300 border border-gray-200 bg-gray-50 rounded-2xl hover:bg-gray-100 dark:border-gray-700 dark:bg-form-input dark:hover:bg-form-input/50 dark:hover:border-gray-600"
                >
                    <div class="flex flex-col items-center">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="text-gray-400 size-6 dark:text-gray-400"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"
                            />
                        </svg>

                        <span class="text-gray-400 dark:text-gray-400">
                            Pilih gambar
                        </span>
                    </div>
                </div>
            </div>

            <input
                :id="props.id"
                type="file"
                :onchange="loadFile"
                class="hidden"
            />
        </label>
    </div>
</template>
