<script setup>
import { defineProps } from "vue";
import { Link } from "@inertiajs/inertia-vue3";

const props = defineProps([
    "pageTitle",
    "parentTitle",
    "parentRoute",
    "pathTitle",
    "topBreadcrumb",
]);
</script>

<template>
    <div
        class="flex flex-col gap-3 mb-6 sm:flex-row sm:items-center sm:justify-between"
        :class="{
            '!flex-col-reverse !items-start': topBreadcrumb,
        }"
    >
        <h2
            v-if="props.pageTitle"
            class="font-semibold text-black text-title-md2 dark:text-white"
        >
            {{ props.pageTitle }}
        </h2>

        <nav v-if="props.parentTitle">
            <ol class="flex items-center gap-2">
                <li>
                    <Link
                        class="font-medium hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-400"
                        :to="props.parentRoute"
                    >
                        {{ props.parentTitle }} /
                    </Link>
                </li>
                <li class="font-medium text-primary dark:text-secondary">
                    {{ props.pathTitle ?? props.pageTitle }}
                </li>
            </ol>
        </nav>
    </div>
</template>
