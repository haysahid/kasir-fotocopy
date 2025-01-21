<script setup lang="ts">
import DefaultLayout from "@/layouts/DefaultLayout.vue";
import PageSection from "@/components/Sections/PageSection.vue";
import StoreConfigForm from "./setting/StoreConfigForm.vue";
import StoreForm from "@/pages/admin/main/store/StoreForm.vue";
import { useUserStore } from "@/stores/user";

const userStore = useUserStore();

function refresh() {
    userStore.fetchUser();
}
</script>

<template>
    <DefaultLayout>
        <div data-aos="fade-up" data-aos-once="true">
            <PageSection
                :page-title="'Pengaturan'"
                id="pagetop"
                class="col-span-12 mb-4 text-sm xl:col-span-8"
            >
            </PageSection>

            <div class="flex flex-col gap-8 lg:flex-row">
                <StoreForm
                    class="w-full"
                    :item="userStore.user.store[0]"
                    :showCloseButton="false"
                    @close="refresh"
                />
                <StoreConfigForm class="w-full" />
            </div>
        </div>
    </DefaultLayout>
</template>
