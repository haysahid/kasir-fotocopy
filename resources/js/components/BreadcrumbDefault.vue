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
        <ol class="flex items-center gap-2">
            <li v-for="(item, i) in props.items" class="text-sm">
                <Link
                    v-if="item.to"
                    :href="item.to"
                    class="font-medium text-gray-500 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-400"
                >
                    <span>{{ item.text }}</span>
                </Link>

                <span
                    v-else
                    class="font-medium text-gray-500 dark:text-gray-500"
                >
                    {{ item.text }}
                </span>

                <span
                    v-if="i < props.items.length - 1"
                    class="text-gray-500 dark:text-gray-500"
                >
                    /
                </span>
            </li>
        </ol>
    </nav>
</template>
