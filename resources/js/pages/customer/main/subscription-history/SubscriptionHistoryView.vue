<script setup>
import DefaultLayout from "@/layouts/DefaultLayout.vue";
import PageSection from "@/components/Sections/PageSection.vue";
import BreadcrumbDefault from "@/components/BreadcrumbDefault.vue";
import { useUserStore } from "@/stores/user";
import { ref, computed, watch, onMounted } from "vue";
import StatusLabel from "@/components/StatusLabel.vue";
import BaseButton from "@/components/landing/BaseButton.vue";
import { Link } from "@inertiajs/inertia-vue3";

const userStore = useUserStore();

const query = ref({
    limit: null,
    page: 1,
    search: null,
});

async function getData(params) {
    await userStore.getSubscriptionHistory(params);
}

const items = computed(() => {
    if (!userStore.subscriptionHistoryData) {
        return [];
    }

    return userStore.subscriptionHistoryData.data;
});

watch(
    () => query.value.search,
    () => {
        getData(query.value);
    }
);

const changePage = (page) => {
    userStore.query.page = page;

    getData();

    document
        .getElementById("pagetop")
        ?.scrollIntoView({ block: "start", behavior: "smooth" });
};

onMounted(() => {
    getData();
});
</script>

<template>
    <DefaultLayout>
        <div
            data-aos="fade-up"
            data-aos-once="true"
            class="px-0 mx-auto md:px-8 max-w-7xl"
        >
            <div class="flex flex-col mb-6">
                <PageSection
                    pageTitle="Riwayat Langganan"
                    class="!flex-row sm:!flex-row"
                >
                    <Link :href="route('home')">
                        <BaseButton
                            class="w-full font-medium px-6 py-3 bg-gradient-to-r from-[#468ef9] to-[#0c66ee] border border-[#0c66ee] text-gray-200 text-nowrap"
                        >
                            Langganan Baru
                        </BaseButton>
                    </Link>
                </PageSection>
                <BreadcrumbDefault
                    :items="[
                        { text: 'Profile', to: '/profile' },
                        { text: 'Riwayat Langganan', to: null },
                    ]"
                />
            </div>

            <!-- Table -->
            <div class="flex flex-col overflow-x-auto">
                <table class="table-fixed">
                    <thead>
                        <tr
                            class="rounded-sm text-gray-500 bg-gray-100 dark:bg-gray-700 [&>th]:py-2.5 [&>th]:px-4 [&>th]:text-left duration-300 ease-linear"
                        >
                            <th>
                                <h5
                                    class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                                >
                                    Tgl. Dibuat
                                </h5>
                            </th>

                            <th>
                                <h5
                                    class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                                >
                                    Kustomer
                                </h5>
                            </th>

                            <th>
                                <h5
                                    class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                                >
                                    Paket
                                </h5>
                            </th>

                            <th>
                                <h5
                                    class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                                >
                                    Total
                                </h5>
                            </th>

                            <th>
                                <h5
                                    class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                                >
                                    Status
                                </h5>
                            </th>

                            <th>
                                <h5
                                    class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                                >
                                    Durasi
                                </h5>
                            </th>

                            <th>
                                <h5
                                    class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                                >
                                    Tgl. Mulai
                                </h5>
                            </th>

                            <th>
                                <h5
                                    class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                                >
                                    Tgl. Berakhir
                                </h5>
                            </th>

                            <th class="w-0 max-sm:text-end">
                                <h5
                                    class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                                >
                                    Tagihan
                                </h5>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="(item, key) in items"
                            :key="key"
                            class="hover:bg-secondary hover:bg-opacity-10 dark:hover:bg-opacity-5 [&>td]:py-2.5 [&>td]:px-4 [&>td]:text-sm duration-300 ease-linear"
                            :class="{
                                'border-b border-stroke dark:border-strokedark':
                                    key !== items.length - 1,
                            }"
                        >
                            <!-- Date Created -->
                            <td>
                                <p class="text-gray-900 dark:text-gray-200">
                                    {{
                                        $formatDate.formatDate(item.created_at)
                                    }}
                                </p>
                                <!-- <p
                                    v-if="item.id"
                                    class="mt-0.5 text-xs text-gray-400 dark:text-gray-400"
                                >
                                    {{ item.id }}
                                </p> -->
                            </td>

                            <!-- Customer -->
                            <td>
                                <p
                                    class="text-gray-900 dark:text-gray-200 line-clamp-2 overflow-ellipsis"
                                >
                                    {{ item.customer?.name }}
                                </p>
                            </td>

                            <!-- Plan -->
                            <td>
                                <p class="text-gray-900 dark:text-gray-200">
                                    {{ item.plan?.name }}
                                </p>
                            </td>

                            <!-- Total Price -->
                            <td>
                                <p class="text-gray-900 dark:text-gray-200">
                                    Rp {{ $formatCurrency(item.amount) }}
                                </p>
                            </td>

                            <!-- Status -->
                            <td>
                                <StatusLabel
                                    v-if="item.status == 'Active'"
                                    :name="item.status"
                                    status="success"
                                />

                                <StatusLabel
                                    v-else-if="item.status == 'Pending Payment'"
                                    :name="item.status"
                                    status="warning"
                                />

                                <StatusLabel
                                    v-else-if="item.status == 'Expired'"
                                    :name="item.status"
                                    status="danger"
                                />

                                <StatusLabel v-else :name="item.status" />
                            </td>

                            <!-- Duration -->
                            <td>
                                <p class="text-gray-900 dark:text-gray-200">
                                    {{ item.duration_text }}
                                </p>
                            </td>

                            <!-- Start Date -->
                            <td>
                                <p class="text-gray-900 dark:text-gray-200">
                                    {{
                                        $formatDate.formatDate(
                                            item.date_subscribed,
                                            {
                                                year: "numeric",
                                                month: "short",
                                                day: "numeric",
                                            }
                                        )
                                    }}
                                </p>
                            </td>

                            <!-- End Date -->
                            <td>
                                <p class="text-gray-900 dark:text-gray-200">
                                    {{
                                        $formatDate.formatDate(item.valid_to, {
                                            year: "numeric",
                                            month: "short",
                                            day: "numeric",
                                        })
                                    }}
                                </p>
                            </td>

                            <!-- Actions -->
                            <td class="py-2.5 px-4">
                                <Link
                                    :href="
                                        route('invoice.detail', {
                                            id: item.invoice_id,
                                        })
                                    "
                                    class="flex items-center justify-center gap-1 text-sm text-right text-primary dark:text-secondary hover:text-primary/90 dark:hover:text-secondary/90 text-nowrap"
                                >
                                    <p>Lihat Tagihan</p>

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        class="w-4 h-4 pt-[1.5px]"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 5l7 7-7 7"
                                        />
                                    </svg>
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div
                    class="flex flex-col items-start justify-between gap-2 py-6 sm:items-center sm:flex-row"
                >
                    <vue-awesome-paginate
                        v-if="userStore?.subscriptionHistoryData?.current_page"
                        :total-items="userStore.subscriptionHistoryData.total"
                        :items-per-page="
                            userStore.subscriptionHistoryData.per_page
                        "
                        :max-pages-shown="5"
                        v-model="userStore.subscriptionHistoryData.current_page"
                        @click="changePage"
                    />
                    <p class="text-xs font-light text-gray-400">
                        Showing
                        {{ userStore.subscriptionHistoryData?.from }} to
                        {{ userStore.subscriptionHistoryData?.to }} of
                        {{ userStore.subscriptionHistoryData?.total }}
                        entries
                    </p>
                </div>
            </div>
        </div>
    </DefaultLayout>
</template>
