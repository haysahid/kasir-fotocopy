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

            <div class="flex gap-8">
                <StoreForm
                    class="max-sm:w-full sm:min-w-[400px] max-w-[400px]"
                    :item="userStore.user.store[0]"
                    :showCloseButton="false"
                    @close="refresh"
                />
                <StoreConfigForm
                    class="max-sm:w-full sm:min-w-[500px] max-w-[500px]"
                />
            </div>
        </div>
    </DefaultLayout>
</template>
