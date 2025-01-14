<script setup>
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import { Link } from "@inertiajs/inertia-vue3";

const props = defineProps({
    title: String,
    value: String | Number,
    link: String,
});
</script>

<template>
    <Link :href="props.link">
        <DefaultCard
            :showShadow="false"
            :showBorder="false"
            class="p-6 cursor-pointer group hover:bg-indigo-100 dark:hover:bg-slate-700/50"
        >
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
                    class="w-6 h-6 text-gray-300 duration-300 ease-linear dark:text-gray-600 group-hover:text-gray-500 dark:group-hover:text-gray-400"
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
    </Link>
</template>
