<script setup>
import BaseNavbar from "@/components/landing/BaseNavbar.vue";
import { useUserStore } from "@/stores/user";
import { onMounted } from "vue";
import { usePlanStore } from "@/stores/plan";
import SubscriptionPlanItem from "./SubscriptionPlanItem.vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import BaseButton from "@/components/landing/BaseButton.vue";
import { ref, computed } from "vue";
import PageSection from "@/components/Sections/PageSection.vue";
import BaseLayout from "@/layouts/BaseLayout.vue";
import BreadcrumbDefault from "@/components/BreadcrumbDefault.vue";
import CheckoutDetail from "./CheckoutDetail.vue";
import { useSubscribeStore } from "@/stores/subscribe";

const userStore = useUserStore();
const planStore = usePlanStore();
const subscribeStore = useSubscribeStore();

const selectedQuantity = ref(1);
const quantityOptions = [1, 3, 6, 12];

function selectQuantity(quantity) {
    selectedQuantity.value = quantity;
}

async function checkout() {
    // Redirect to login page if user is not logged in
    if (!userStore.user) {
        window.location = route("login", {
            redirect: window.location.href,
        });
        return;
    }

    // Check if user is admin
    if (userStore.user.role_id < 3) {
        alert("Anda tidak memiliki akses untuk berlangganan paket ini.");
        return;
    }

    const planId = route().params.id;
    await subscribeStore.createInvoice(planId, selectedQuantity.value);

    if (subscribeStore.invoice) {
        window.location = route("invoice.detail", {
            id: subscribeStore.invoice.id,
        });
    }
}

async function getDetail() {
    const planId = route().params.id;
    await planStore.getItem(planId);

    if (planStore.item.price === 0) {
        quantityOptions.value = [planStore.item.duration];
        selectedQuantity.value = planStore.item.duration;
    }
}

const amount = computed(() => {
    return planStore.item.price * selectedQuantity.value;
});

const total = computed(() => {
    return amount.value * 1.12;
});

const getDateRange = computed(() => {
    const startDate = new Date();
    const endDate = new Date();

    if (planStore.item.duration_type === "days") {
        endDate.setDate(startDate.getDate() + (selectedQuantity.value - 1));
    } else if (planStore.item.duration_type === "months") {
        endDate.setMonth(startDate.getMonth() + selectedQuantity.value);
    } else if (planStore.item.duration_type === "years") {
        endDate.setFullYear(startDate.getFullYear() + selectedQuantity.value);
    }

    return {
        start: startDate,
        end: endDate,
    };
});

function translateDurationType(type) {
    switch (type) {
        case "days":
            return "hari";
        case "months":
            return "bulan";
        case "years":
            return "tahun";
        default:
            return "";
    }
}

function getDurationText(duration, type) {
    console.log(duration, type);

    if (duration === 1) {
        return translateDurationType(type);
    }

    return duration + " " + translateDurationType(type);
}

onMounted(() => {
    getDetail();
});
</script>

<template>
    <BaseLayout class="h-full">
        <div class="relative overflow-hidden">
            <div
                data-aos="fade-up"
                data-aos-once="true"
                class="px-8 mx-auto max-w-7xl"
            >
                <div
                    class="flex flex-col gap-2 mb-6 md:items-center md:flex-row md:justify-between"
                >
                    <PageSection pageTitle="Detail Paket Berlangganan" />
                    <BreadcrumbDefault
                        :items="[
                            { text: 'Beranda', to: '/' },
                            { text: 'Paket Berlangganan', to: null },
                            {
                                text: planStore.item
                                    ? planStore.item.name
                                    : 'Detail Paket',
                                to: null,
                            },
                        ]"
                    />
                </div>

                <div
                    class="flex flex-col items-start justify-center w-full gap-8 lg:flex-row sm:pt-0"
                >
                    <DefaultCard
                        class="flex flex-col w-full gap-6 px-5 py-6 lg:w-3/5 rounded-2xl"
                    >
                        <SubscriptionPlanItem
                            v-if="planStore.item"
                            :title="planStore.item.name"
                            :description="planStore.item.description"
                            :price="planStore.item.price"
                            :duration="
                                getDurationText(
                                    planStore.item.duration,
                                    planStore.item.duration_type
                                )
                            "
                            :options="planStore.item.options"
                            :showButton="false"
                        />

                        <!-- Quantity Options -->
                        <div
                            v-if="planStore.item.price > 0"
                            class="flex flex-wrap gap-4"
                        >
                            <button
                                v-for="quantity in quantityOptions"
                                :key="quantity"
                                @click="() => selectQuantity(quantity)"
                                :class="{
                                    'bg-primary text-gray-200':
                                        selectedQuantity === quantity,
                                    'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600':
                                        selectedQuantity !== quantity,
                                }"
                                class="px-6 py-2.5 rounded-lg duration-300 ease-linear"
                            >
                                <p class="font-semibold">
                                    {{ quantity }}
                                    {{
                                        translateDurationType(
                                            planStore.item.duration_type
                                        )
                                    }}
                                </p>

                                <p class="text-sm italic">
                                    Rp
                                    {{
                                        $formatCurrency(
                                            planStore.item.price * quantity
                                        )
                                    }}
                                </p>
                            </button>
                        </div>
                    </DefaultCard>

                    <DefaultCard
                        class="flex flex-col w-full gap-4 px-5 py-6 lg:w-2/5 rounded-2xl"
                    >
                        <!-- Checkout Detail -->
                        <h2 class="text-lg font-bold dark:text-gray-200">
                            Detail Pembayaran
                        </h2>

                        <CheckoutDetail
                            :items="[
                                {
                                    label: 'Paket Berlangganan',
                                    value: planStore.item.name,
                                },
                                {
                                    label: 'Durasi',
                                    value:
                                        $formatDate.formatDate(
                                            getDateRange.start,
                                            {
                                                month: 'short',
                                                year: 'numeric',
                                                day: 'numeric',
                                            }
                                        ) +
                                        ' - ' +
                                        $formatDate.formatDate(
                                            getDateRange.end,
                                            {
                                                month: 'short',
                                                year: 'numeric',
                                                day: 'numeric',
                                            }
                                        ),
                                },
                            ]"
                            :total="total"
                            :amount="amount"
                        />

                        <BaseButton
                            @click="checkout"
                            class="w-full font-medium px-6 py-3 bg-gradient-to-r from-[#468ef9] to-[#0c66ee] border border-[#0c66ee] text-gray-200"
                        >
                            Lanjutkan Pembayaran
                        </BaseButton>
                    </DefaultCard>
                </div>
            </div>
        </div>
    </BaseLayout>
</template>
