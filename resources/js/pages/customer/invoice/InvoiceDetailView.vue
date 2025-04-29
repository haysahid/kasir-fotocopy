<script setup>
import BaseNavbar from "@/components/landing/BaseNavbar.vue";
import { useUserStore } from "@/stores/user";
import { onMounted } from "vue";
import { usePlanStore } from "@/stores/plan";
import SubscriptionPlanItem from "../public/SubscriptionPlanItem.vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import CustomButton from "@/components/Forms/CustomButton.vue";
import { ref, computed } from "vue";
import PageSection from "@/components/Sections/PageSection.vue";
import BaseLayout from "@/layouts/BaseLayout.vue";
import BreadcrumbDefault from "@/components/BreadcrumbDefault.vue";
import { useSubscribeStore } from "@/stores/subscribe";
import CheckoutDetail from "../public/CheckoutDetail.vue";
import { usePaymentMethodStore } from "@/stores/payment_method";
import { Link } from "@inertiajs/inertia-vue3";

const userStore = useUserStore();
const subscribeStore = useSubscribeStore();
const paymentMethodStore = usePaymentMethodStore();

const snapStatus = ref("");

const plan = computed(() => {
    return subscribeStore.invoice
        ? subscribeStore.invoice.plan_history.plan
        : null;
});

const quantity = computed(() => {
    return subscribeStore.invoice
        ? subscribeStore.invoice.plan_history.quantity
        : 1;
});

async function getData() {
    const invoiceId = route().params.id;

    await subscribeStore.getInvoiceDetail(invoiceId);

    await paymentMethodStore.getDropdown();
}

async function pay() {
    if (subscribeStore.snapToken) {
        await showSnap(subscribeStore.snapToken);
        return;
    }

    const response = await subscribeStore.createSnapToken(
        subscribeStore.invoice.id
    );

    if (response) {
        await showSnap(response.result.snap_token);
    }
}

async function showSnap(snapToken) {
    snapStatus.value = "loading";

    window.snap.pay(snapToken, {
        onSuccess: async function (result) {
            snapStatus.value = "success";

            let paymentMethodId = 1;

            if (result.payment_type === "bank_transfer") {
                paymentMethodId = 1;
            } else {
                paymentMethodId = 2;
            }

            const amount = parseInt(result.gross_amount);

            await subscribeStore.createInvoicePayment(
                subscribeStore.invoice.id,
                paymentMethodId,
                amount,
                "Transfer melalui Midtrans"
            );

            await subscribeStore.getInvoiceDetail(subscribeStore.invoice.id);
        },
        onPending: async function (result) {
            console.log("pending", result);
            snapStatus.value = "pending";
        },
        onError: async function (result) {
            console.log("error", result);
            snapStatus.value = "error";
        },
        onClose: async function () {
            console.log(
                "customer closed the popup without finishing the payment"
            );
            snapStatus.value = "close";
        },
    });
}

async function initScript() {
    const snapScript = "https://app.sandbox.midtrans.com/snap/snap.js";
    const clientKey = subscribeStore.midtransClientKey;

    const script = document.createElement("script");
    script.src = snapScript;
    script.setAttribute("data-client-key", clientKey);
    script.async = true;

    document.body.appendChild(script);

    return () => {
        document.body.removeChild(script);
    };
}

const amount = computed(() => {
    return plan.value.price * quantity.value;
});

const total = computed(() => {
    return amount.value * 1.11;
});

const getDateRange = computed(() => {
    const startDate = new Date();
    const endDate = new Date();

    if (plan.value.duration_type === "days") {
        endDate.setDate(startDate.getDate() + (quantity.value - 1));
    } else if (plan.value.duration_type === "months") {
        endDate.setMonth(startDate.getMonth() + quantity.value);
    } else if (plan.value.duration_type === "years") {
        endDate.setFullYear(startDate.getFullYear() + quantity.value);
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
    initScript();
    getData();
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
                    <PageSection pageTitle="Detail Tagihan" />
                    <BreadcrumbDefault
                        :items="[
                            { text: 'Beranda', to: '/' },
                            { text: 'Paket Berlangganan', to: null },
                            {
                                text: plan ? plan.name : 'Detail Paket',
                                to: null,
                            },
                            {
                                text: 'Detail Tagihan',
                                to: null,
                            },
                        ]"
                    />
                </div>

                <div
                    v-if="subscribeStore.invoice"
                    class="flex flex-col items-start justify-start w-full gap-8 lg:flex-row sm:pt-0"
                >
                    <DefaultCard
                        class="flex flex-col w-full gap-4 px-5 py-6 rounded-2xl lg:w-1/2"
                    >
                        <div class="flex flex-col gap-1">
                            <h3
                                class="text-lg font-semibold dark:text-gray-200"
                            >
                                Tagihan #{{ subscribeStore.invoice.id }}
                            </h3>

                            <div
                                class="flex justify-between gap-4 text-gray-500 dark:text-gray-400"
                            >
                                <p>Kepada</p>
                                <p class="text-right">
                                    {{
                                        subscribeStore.invoice.subscription
                                            ?.customer?.name
                                    }}
                                </p>
                            </div>

                            <div
                                class="flex justify-between gap-4 text-gray-500 dark:text-gray-400"
                            >
                                <p>Dibuat pada</p>
                                <p class="text-right">
                                    {{
                                        $formatDate.formatDate(
                                            subscribeStore.invoice.created_at
                                        )
                                    }}
                                </p>
                            </div>

                            <div
                                class="flex justify-between gap-4 text-gray-500 dark:text-gray-400"
                            >
                                <p>Batas waktu pembayaran</p>
                                <p
                                    class="font-semibold text-right text-gray-900 dark:text-gray-200"
                                >
                                    {{
                                        $formatDate.formatDate(
                                            subscribeStore.invoice.due_at
                                        )
                                    }}
                                </p>
                            </div>

                            <!-- Status pembayaran -->
                            <div
                                class="flex justify-between gap-4 text-gray-500 dark:text-gray-400"
                            >
                                <p>Status Pembayaran</p>
                                <p
                                    v-if="
                                        subscribeStore.invoice.status ===
                                        'Pending'
                                    "
                                    class="font-semibold text-right text-warning"
                                >
                                    Menunggu Pembayaran
                                </p>
                                <p
                                    v-else-if="
                                        subscribeStore.invoice.status === 'Paid'
                                    "
                                    class="font-semibold text-right text-success"
                                >
                                    Lunas
                                </p>
                                <p
                                    v-else-if="
                                        subscribeStore.invoice.status ===
                                        'Expired'
                                    "
                                    class="font-semibold text-right text-danger"
                                >
                                    Kadaluarsa
                                </p>
                            </div>

                            <div
                                v-if="subscribeStore.invoice.status === 'Paid'"
                                class="flex justify-between gap-4 text-gray-500 dark:text-gray-400"
                            >
                                <p>Dibayar pada</p>
                                <p class="text-right">
                                    {{
                                        $formatDate.formatDate(
                                            subscribeStore.invoice.paid_at
                                        )
                                    }}
                                </p>
                            </div>

                            <div
                                v-if="subscribeStore.invoice.status === 'Paid'"
                                class="flex justify-between gap-4 text-gray-500 dark:text-gray-400"
                            >
                                <p>Metode Pembayaran</p>
                                <p class="text-right">
                                    {{
                                        subscribeStore.invoice
                                            .invoice_payments[0]?.payment_method
                                            ?.name
                                    }}
                                </p>
                            </div>
                        </div>

                        <hr
                            class="h-[1px] border-0 dark:bg-gray-600 bg-gray-300 duration-300 ease-linear"
                        />

                        <CheckoutDetail
                            :items="[
                                {
                                    label: 'Nama Paket',
                                    value: plan.name,
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
                            :amount="amount"
                            :total="total"
                        />

                        <CustomButton
                            v-if="subscribeStore.invoice.status === 'Pending'"
                            @click="pay"
                            :disabled="snapStatus === 'loading'"
                            :loading="snapStatus === 'loading'"
                            class="w-full font-medium px-6 py-3 bg-gradient-to-r from-[#468ef9] to-[#0c66ee] border border-[#0c66ee] text-gray-200"
                        >
                            Bayar Sekarang
                        </CustomButton>

                        <Link
                            v-if="subscribeStore.invoice.status === 'Paid'"
                            :href="route('create-store')"
                        >
                            <CustomButton
                                class="w-full font-medium px-6 py-3 bg-gradient-to-r from-[#468ef9] to-[#0c66ee] border border-[#0c66ee] text-gray-200"
                            >
                                Lanjutkan
                            </CustomButton>
                        </Link>
                    </DefaultCard>
                </div>
            </div>
        </div>
    </BaseLayout>
</template>
