<script setup>
import HeaderArea from "@/components/Header/HeaderArea.vue";
import SidebarArea from "@/components/Sidebar/SidebarArea.vue";
import { useUserStore } from "../stores/user";
import StoreNotFoundAlert from "../pages/customer/main/StoreNotFoundAlert.vue";
import { onMounted } from "vue";

const userStore = useUserStore();
const showContent =
    (userStore.user && route().current() == "profile") ||
    (userStore.user && userStore.user?.store?.length > 0);

onMounted(() => {});
</script>

<template>
    <div
        class="flex h-screen overflow-hidden duration-300 ease-linear bg-gray-50 dark:bg-gray-800"
    >
        <SidebarArea />
        <div
            class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto"
        >
            <HeaderArea />
            <main>
                <div class="p-4 mx-auto max-w-screen-2xl md:p-6 2xl:p-10">
                    <slot v-if="showContent"></slot>

                    <StoreNotFoundAlert
                        v-else
                        data-aos="fade-up"
                        data-aos-once="true"
                        class="mt-24"
                    />
                </div>
            </main>
        </div>
    </div>
</template>
