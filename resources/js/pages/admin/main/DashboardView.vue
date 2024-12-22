<script setup lang="ts">
import AdminDefaultLayout from "@/layouts/AdminDefaultLayout.vue";
import PageSection from "@/components/Sections/PageSection.vue";
import { useUserStore } from "@/stores/user";
import { useDashboardStore } from "@/stores/admin/dashboard";
import CustomBarChart from "@/components/Charts/CustomBarChart.vue";
import { ref, computed, onMounted } from "vue";
import SummaryCard from "./dashboard/SummaryCard.vue";

const userStore = useUserStore();
const dashboardStore = useDashboardStore();

const storeGraph = ref(null);
const summary = ref(null);

async function getStoreGraph() {
    const response = await dashboardStore.getGraph(userStore.user?.store[0].id);

    storeGraph.value = response;
}

async function getSummary() {
    const response = await dashboardStore.getSummary();

    summary.value = response;
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
    // getStoreGraph();
    getSummary();
});
</script>

<template>
    <AdminDefaultLayout>
        <div data-aos="fade-up" data-aos-once="true">
            <PageSection
                v-if="userStore.user?.store && userStore.user?.store.length > 0"
                :page-title="'Dashboard'"
                id="pagetop"
                class="col-span-12 mb-4 text-sm xl:col-span-8"
            >
            </PageSection>

            <div
                v-if="summary"
                class="grid grid-cols-1 gap-4 mb-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4"
            >
                <SummaryCard title="Pengguna" :value="summary.result.users" />
                <SummaryCard
                    title="Pengguna Aktif Berlangganan"
                    :value="summary.result.subscribed_users"
                />
                <SummaryCard title="Toko" :value="summary.result.stores" />
                <SummaryCard
                    title="Total Pemasukan"
                    :value="
                        'Rp ' + $formatCurrency(summary.result.total_income)
                    "
                />
                <SummaryCard
                    title="Rata-rata Pemasukan Bulanan"
                    :value="
                        'Rp ' +
                        $formatCurrency(summary.result.mean_monthly_income)
                    "
                />
            </div>

            <CustomBarChart
                v-if="getChartData"
                title="Pemasukan"
                height="260"
                :chart-data="getChartData"
                :loading="dashboardStore.getGraphStatus === 'loading'"
            />
        </div>
    </AdminDefaultLayout>
</template>
