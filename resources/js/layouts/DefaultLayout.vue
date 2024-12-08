<script setup>
import HeaderArea from "@/components/Header/HeaderArea.vue";
import SidebarArea from "@/components/Sidebar/SidebarArea.vue";
</script>

<template>
    <div class="flex h-screen overflow-hidden">
        <SidebarArea />
        <div
            class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 dark:bg-gray-800"
        >
            <HeaderArea />
            <main>
                <div class="p-4 mx-auto max-w-screen-2xl md:p-6 2xl:p-10">
                    <slot></slot>
                </div>
            </main>
        </div>
    </div>
</template>
