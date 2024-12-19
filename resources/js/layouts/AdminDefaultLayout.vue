<script setup>
import { useUserStore } from "@/stores/user";
import { onMounted } from "vue";
import AdminSidebarArea from "@/components/Sidebar/AdminSidebarArea.vue";
import AdminHeaderArea from "@/components/Header/AdminHeaderArea.vue";

const userStore = useUserStore();

onMounted(() => {});
</script>

<template>
    <div
        class="flex h-screen overflow-hidden duration-300 ease-linear bg-gray-50 dark:bg-gray-800"
    >
        <AdminSidebarArea />
        <div
            class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto"
        >
            <AdminHeaderArea />
            <main>
                <div class="p-4 mx-auto max-w-screen-2xl md:p-6 2xl:p-10">
                    <slot></slot>
                </div>
            </main>
        </div>
    </div>
</template>
