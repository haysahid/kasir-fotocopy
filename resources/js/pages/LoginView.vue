<script setup>
import LoginForm from "./customer/auth/LoginForm.vue";
import BaseNavbar from "@/components/landing/BaseNavbar.vue";
import { useUserStore } from "@/stores/user";
import { onMounted } from "vue";

const userStore = useUserStore();

onMounted(() => {
    if (userStore.user) {
        if (userStore.user.role_id >= 3) {
            window.location.replace(route("dashboard"));
        } else {
            window.location.replace(route("admin.dashboard"));
        }
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
