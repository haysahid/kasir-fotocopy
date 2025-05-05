<script setup>
const emit = defineEmits(["close"]);

const props = defineProps({
    cardTitle: String,
    showCloseButton: Boolean,
    cardWidth: Number,
    showShadow: {
        type: Boolean,
        default: true,
    },
    showBorder: {
        type: Boolean,
        default: true,
    },
});

function close() {
    emit("close", false);
}
</script>

<template>
    <div
        class="duration-300 ease-linear bg-white rounded-lg dark:bg-boxdark"
        :class="{
            'shadow-default': props.showShadow,
            'border border-stroke dark:border-strokedark': props.showBorder,
        }"
    >
        <div
            v-if="cardTitle"
            class="flex justify-between border-b border-stroke py-4 px-6.5 dark:border-strokedark duration-300 ease-linear sticky top-0 z-10 bg-white dark:bg-boxdark"
        >
            <h3 class="font-medium text-gray-900 dark:text-gray-200">
                {{ props.cardTitle }}
            </h3>
            <button
                v-if="showCloseButton"
                @click="close"
                class="text-gray-900 hover:text-danger dark:text-gray-200 dark:hover:text-danger"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    class="w-5 h-5"
                >
                    <path
                        d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"
                    />
                </svg>
            </button>
        </div>
        <slot></slot>
    </div>
</template>
