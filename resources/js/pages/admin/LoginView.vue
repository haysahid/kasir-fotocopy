<script setup>
import LoginForm from "./auth/LoginForm.vue";
import BaseNavbar from "@/components/landing/BaseNavbar.vue";
import { useUserStore } from "@/stores/user";
import { onMounted } from "vue";

const userStore = useUserStore();

onMounted(() => {
    if (userStore.user) {
        window.location.replace(route("dashboard"));
    }
});
</script>

<template>
    <main class="h-full">
        <BaseNavbar :show-nav-link="true" />
        <div class="relative overflow-hidden">
            <div class="mx-auto max-w-7xl">
                <div
                    class="flex flex-col items-center justify-center w-full min-h-[80vh] py-6 sm:pt-0"
                >
                    <LoginForm data-aos="fade-up" data-aos-once="true" />
                </div>
            </div>
        </div>
    </main>
</template>
