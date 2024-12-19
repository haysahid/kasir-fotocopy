<script setup>
import { ref, computed, onMounted } from "vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";

const props = defineProps({
    title: String,
    value: String | Number,
});
</script>

<template>
    <DefaultCard :showShadow="false" :showBorder="false" class="p-6">
        <div class="flex items-center justify-between gap-8">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-300">
                    {{ title }}
                </p>
                <p class="text-lg font-semibold dark:text-gray-100">
                    {{ value }}
                </p>
            </div>
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-6 h-6 text-gray-300 duration-300 ease-linear dark:text-gray-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 5l7 7-7 7"
                />
            </svg>
        </div>
    </DefaultCard>
</template>
