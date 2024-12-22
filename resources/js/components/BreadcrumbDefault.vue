<script setup>
import { defineProps } from "vue";
import { Link } from "@inertiajs/inertia-vue3";

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <nav>
        <span v-for="(item, i) in props.items" class="text-sm">
            <span v-if="item.to">
                <Link
                    :href="item.to"
                    class="font-medium text-gray-500 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-400"
                >
                    {{ item.text }}
                </Link>
            </span>

            <span v-else class="font-medium text-gray-500 dark:text-gray-500">
                {{ item.text }}
            </span>

            <span
                v-if="i < props.items.length - 1"
                class="text-gray-500 dark:text-gray-500"
            >
                /
            </span>
        </span>
    </nav>
</template>
