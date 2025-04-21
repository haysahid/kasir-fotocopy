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
            class="flex-wrap justify-center gap-4 py-8 text-sm text-center text-gray-500 dark:text-gray-600 sm:py-4"
        >
            <div>
                &copy; Copyright {{ configStore.copyrightYear }}
                <a
                    :href="configStore.copyrightUrl"
                    class="hover:text-primary dark:hover:text-secondary"
                >
                    {{ configStore.copyrightName }}
                </a>
            </div>
            <div
                v-if="configStore.supportedBy"
                :href="configStore.supportedByUrl"
            >
                Supported by
                <a
                    :href="configStore.supportedByUrl"
                    class="hover:text-primary dark:hover:text-secondary"
                >
                    {{ configStore.supportedBy }}
                </a>
            </div>
        </div>
    </footer>
</template>
<script>
export default {
    name: "BaseFooter",
};
</script>
