<script setup lang="ts">
import AdminDefaultLayout from "@/layouts/AdminDefaultLayout.vue";
import PageSection from "@/components/Sections/PageSection.vue";
import AppConfigForm from "./setting/AppConfigForm.vue";
import { useUserStore } from "@/stores/user";

const userStore = useUserStore();

function refresh() {
    userStore.fetchUser();
}
</script>

<template>
    <AdminDefaultLayout>
        <div data-aos="fade-up" data-aos-once="true">
            <PageSection
                :page-title="'Pengaturan'"
                id="pagetop"
                class="col-span-12 mb-4 text-sm xl:col-span-8"
            >
            </PageSection>

            <div class="flex flex-col gap-8 lg:flex-row">
                <AppConfigForm class="w-full" />
            </div>
        </div>
    </AdminDefaultLayout>
</template>
