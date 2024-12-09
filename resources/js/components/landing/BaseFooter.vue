<script setup>
import NavLink from "./NavLink.vue";
import { useConfigStore } from "@/stores/config";

const configStore = useConfigStore();
</script>

<template>
    <footer class="max-w-screen-xl px-8 mx-auto">
        <div class="w-full border-y border-[#DDDDDD]"></div>
        <div
            class="py-8 sm:py-4 text-center text-sm text-[#666666] hover:text-gray-900"
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
