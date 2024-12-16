<script setup lang="ts">
import DefaultLayout from "@/layouts/DefaultLayout.vue";
import PageSection from "@/components/Sections/PageSection.vue";
import SettingForm from "./setting/SettingForm.vue";
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

            <SettingForm class="max-sm:w-full sm:min-w-[400px] max-w-[400px]" />
        </div>
    </DefaultLayout>
</template>
