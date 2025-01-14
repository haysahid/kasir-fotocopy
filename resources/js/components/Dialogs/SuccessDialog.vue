<script setup>
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import CustomButton from "@/components/Forms/CustomButton.vue";

const props = defineProps({
    showCloseButton: Boolean,
    title: {
        type: String,
        default: "Success",
    },
    description: {
        type: String,
        default: "Transaksi berhasil",
    },
    sales: {
        type: Object,
        default: null,
    },
});
const emit = defineEmits(["close"]);

function close(value) {
    emit("close", value);
}
</script>

<template>
    <DefaultCard
        :show-close-button="props.showCloseButton"
        @close="close(false)"
    >
        <div class="px-6.5 py-8 flex flex-col items-center">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="mb-4 text-green-400 size-20"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                />
            </svg>

            <h2
                class="mb-4 text-center text-gray-700 text-title-lg dark:text-gray-200"
            >
                {{ props.title }}
            </h2>

            <p class="mb-4 text-center text-gray-500 dark:text-gray-400">
                {{ props.description }}
            </p>

            <template v-if="props.sales">
                <p class="mb-4 text-center text-gray-500 dark:text-gray-400">
                    Kembali: Rp {{ $formatCurrency(props.sales.return) }}
                </p>
            </template>

            <div
                class="flex flex-col-reverse w-full mt-4 gap-x-2 gap-y-2 sm:flex-row"
            >
                <slot> </slot>
                <CustomButton
                    @click="close(true)"
                    :is-full="true"
                    padding="py-2.5 px-6"
                >
                    OK
                </CustomButton>
            </div>
        </div>
    </DefaultCard>
</template>
