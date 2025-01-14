<script setup lang="ts">
import DefaultLayout from "@/layouts/DefaultLayout.vue";
import PageSection from "@/components/Sections/PageSection.vue";
import { useUserStore } from "@/stores/user";
import { useStoreStore } from "@/stores/store";
import CustomBarChart from "@/components/Charts/CustomBarChart.vue";
import { ref, computed, onMounted } from "vue";
import SummaryCard from "./dashboard/SummaryCard.vue";

const userStore = useUserStore();
const storeStore = useStoreStore();

const storeGraph = ref(null);
const storeSummary = ref(null);

async function getStoreGraph() {
    if (!userStore.hasStore) return;

    const response = await storeStore.getGraph(userStore.user?.store[0].id);

    storeGraph.value = response;
}

async function getStoreSummary() {
    if (!userStore.hasStore) return;

    const response = await storeStore.getSummary(userStore.user?.store[0].id);

    storeSummary.value = response;
}

const getChartData = computed(() => {
    if (!userStore.hasStore) return null;
    if (!storeGraph.value) return null;

    const labels = storeGraph.value.result.labels;

    return {
        series: [
            {
                name: "Pemasukan",
                data: storeGraph.value.result.revenue,
            },
        ],
        labels: labels,
    };
});

onMounted(() => {
    getStoreGraph();
    getStoreSummary();
});
</script>

<template>
    <DefaultLayout>
        <div data-aos="fade-up" data-aos-once="true">
            <PageSection
                v-if="userStore.user?.store && userStore.user?.store.length > 0"
                :page-title="'Dashboard'"
                id="pagetop"
                class="col-span-12 mb-4 text-sm xl:col-span-8"
            >
            </PageSection>

            <div
                v-if="storeSummary"
                class="grid grid-cols-1 gap-4 mb-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4"
            >
                <SummaryCard
                    title="Pembelian"
                    :value="
                        'Rp ' +
                        $formatCurrency(storeSummary.result.total_purchases)
                    "
                    link="/purchase-history"
                />
                <SummaryCard
                    title="Penjualan"
                    :value="
                        'Rp ' + $formatCurrency(storeSummary.result.total_sales)
                    "
                    link="/sales-history"
                />
                <SummaryCard
                    title="Total Pembelian"
                    :value="storeSummary.result.total_purchases_count"
                    link="/purchase-history"
                />
                <SummaryCard
                    title="Total Penjualan"
                    :value="storeSummary.result.total_sales_count"
                    link="/sales-history"
                />
                <SummaryCard
                    title="Rata-rata Pembelian"
                    :value="
                        'Rp ' +
                        $formatCurrency(storeSummary.result.mean_purchases)
                    "
                    link="/purchase-history"
                />
                <SummaryCard
                    title="Rata-rata Penjualan"
                    :value="
                        'Rp ' + $formatCurrency(storeSummary.result.mean_sales)
                    "
                    link="/sales-history"
                />
                <SummaryCard
                    title="Produk Terjual"
                    :value="storeSummary.result.sold_products"
                    link="/product"
                />
            </div>

            <CustomBarChart
                v-if="getChartData"
                title="Pemasukan"
                height="260"
                :chart-data="getChartData"
                :loading="storeStore.getGraphStatus === 'loading'"
            />
        </div>
    </DefaultLayout>
</template>
