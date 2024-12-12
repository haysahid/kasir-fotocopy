<script setup lang="ts">
import DefaultLayout from "@/layouts/DefaultLayout.vue";
import PageSection from "@/components/Sections/PageSection.vue";
import { useUserStore } from "@/stores/user";
import StoreNotFoundAlert from "./StoreNotFoundAlert.vue";

const userStore = useUserStore();
</script>

<template>
    <DefaultLayout>
        <div data-aos="fade-up" data-aos-once="true">
            <PageSection
                v-if="userStore.user?.store && userStore.user?.store.length > 0"
                :page-title="'Dashboard'"
                id="pagetop"
                class="col-span-12 text-sm xl:col-span-8"
            >
            </PageSection>
        </div>
    </DefaultLayout>
</template>
