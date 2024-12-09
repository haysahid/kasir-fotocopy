<script setup lang="ts">
import DefaultLayout from "@/layouts/DefaultLayout.vue";
import PageSection from "@/components/Sections/PageSection.vue";
</script>

<template>
    <DefaultLayout>
        <div data-aos="fade-up" data-aos-once="true">
            <PageSection
                :page-title="'Pengaturan'"
                id="pagetop"
                class="col-span-12 text-sm xl:col-span-8"
            >
            </PageSection>
        </div>
    </DefaultLayout>
</template>
