<script setup>
import LoginForm from "@/components/authentication/LoginForm.vue";
import BaseNavbar from "@/components/landing/BaseNavbar.vue";
</script>

<template>
    <main>
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
