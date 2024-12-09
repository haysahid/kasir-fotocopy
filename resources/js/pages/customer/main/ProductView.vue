<script setup lang="ts">
import DefaultLayout from "@/layouts/DefaultLayout.vue";
import PageSection from "@/components/Sections/PageSection.vue";
import ProductTable from "./product/ProductTable.vue";
</script>

<template>
    <DefaultLayout>
        <div data-aos="fade-up" data-aos-once="true">
            <PageSection
                :page-title="'Produk'"
                id="pagetop"
                class="col-span-12 mb-4 text-sm xl:col-span-8"
            >
            </PageSection>
            <ProductTable />
        </div>
    </DefaultLayout>
</template>
