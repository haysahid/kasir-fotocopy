<script setup>
import CustomButton from "@/components/Forms/CustomButton.vue";
import BaseButton from "@/components/landing/BaseButton.vue";

const props = defineProps({
    title: String,
    description: String,
    price: Number,
    duration: String,
    options: {
        type: Array,
        default: () => [],
    },
    priority: Boolean,
});
</script>

<template>
    <div class="flex flex-col h-full gap-4">
        <div>
            <div>
                <h2 class="text-xl font-bold dark:text-white">
                    {{ props.title }}
                </h2>
                <p class="text-gray-500">{{ props.description }}</p>
            </div>

            <div>
                <span
                    class="text-xl font-bold text-primary dark:text-secondary"
                >
                    Rp {{ $formatCurrency(props.price) }}
                </span>
                <span class="dark:text-gray-300"> / {{ props.duration }}</span>
            </div>
        </div>

        <div class="flex flex-col h-full gap-2">
            <div
                v-for="option in props.options"
                :key="option.id"
                class="flex items-center gap-2"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5 text-success"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 13l4 4L19 7"
                    />
                </svg>
                <span class="text-gray-500 dark:text-gray-400">{{
                    option.name
                }}</span>
            </div>
        </div>

        <div class="mt-4">
            <BaseButton
                v-if="props.priority"
                class="w-full font-medium px-6 py-3 bg-gradient-to-r from-[#468ef9] to-[#0c66ee] border border-[#0c66ee] text-white"
            >
                Pilih Paket
            </BaseButton>

            <BaseButton
                v-else
                class="w-full px-6 py-3 bg-inherit text-gradient border border-[#0c66ee] text-[#0c66ee]"
            >
                Pilih Paket
            </BaseButton>
        </div>
    </div>
</template>
