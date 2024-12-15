<script setup lang="ts">
import DefaultLayout from "@/layouts/DefaultLayout.vue";
import PageSection from "@/components/Sections/PageSection.vue";
import { ref, watch } from "vue";
import PurchaseHistoryTable from "./purchase-history/PurchaseHistoryTable.vue";

const tableData = ref(null);

const query = ref({
    limit: null,
    page: 1,
    search: null,
});

watch(
    () => query.value.search,
    () => {
        tableData.value?.getData(query.value);
    }
);
</script>

<template>
    <DefaultLayout>
        <div data-aos="fade-up" data-aos-once="true">
            <PageSection
                :page-title="'Riwayat Pembelian'"
                id="pagetop"
                class="col-span-12 mb-4 text-sm xl:col-span-8"
            >
            </PageSection>

            <PurchaseHistoryTable ref="tableData" />
        </div>
    </DefaultLayout>
</template>
