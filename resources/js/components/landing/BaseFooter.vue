<script setup>
import NavLink from "./NavLink.vue";
import { useConfigStore } from "@/stores/config";

const configStore = useConfigStore();
</script>

<template>
    <footer
        data-aos="fade-up"
        data-aos-once="true"
        class="max-w-screen-xl px-8 mx-auto mt-16"
    >
        <div
            class="w-full border-gray-100 dark:border-gray-800 border-y-[1px] duration-300 ease-linear"
        ></div>
        <div
            class="py-8 text-sm text-center text-gray-500 dark:text-gray-600 sm:py-4 hover:text-gray-900"
        >
            &copy; Copyright {{ configStore.copyrightYear }}
            {{ configStore.copyrightName }}. All rights reserved
        </div>
    </footer>
</template>
<script>
export default {
    name: "BaseFooter",
};
</script>
