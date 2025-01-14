<script setup>
import { ref, onUpdated, watch, onMounted, computed } from "vue";
import InputGroup from "@/components/Forms/InputGroup.vue";
import AlertWarning from "@/components/Alerts/AlertWarning.vue";
import CustomButton from "@/components/Forms/CustomButton.vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import SuccessDialog from "@/components/Dialogs/SuccessDialog.vue";
import { useReportStore } from "@/stores/report";
import { useUserStore } from "@/stores/user";
import SelectGroup from "@/components/Forms/SelectGroup.vue";
import CustomDatePicker from "@/components/Forms/CustomDatePicker.vue";
import SalesReportTable from "./SalesReportTable.vue";
import PurchaseReportTable from "./PurchaseReportTable.vue";

const userStore = useUserStore();
const reportStore = useReportStore();

const form = ref({
    report_type: "sales",
    start_date: null,
    end_date: null,
});

const formValidation = ref({
    report_type: "",
    start_date: "",
    end_date: "",
});

function clearErrorMessage() {
    reportStore.errorMessage = "";
}

async function getReport() {
    if (!validateGetReport()) return;

    if (form.value.report_type === "sales") {
        await reportStore.getSalesReport(
            form.value.start_date,
            form.value.end_date
        );
    } else if (form.value.report_type === "purchase") {
        await reportStore.getPurchaseReport(
            form.value.start_date,
            form.value.end_date
        );
    }
}

const b64toUrl = async (base64Data) => {
    const r = await fetch(base64Data);
    const blob = await r.blob();
    return URL.createObjectURL(blob);
};

async function printReport() {
    const element = document.querySelector("#pdf-content");

    if (!element) return;

    const date = new Date();
    const dateString = date.toISOString().slice(0, 10);
    const fileName = `laporan-${getReportType()}-${dateString}`;

    let content = `
        <html>
            <head>
                <title>${fileName}</title>
                <style>
                    @page {
                        size: A4;
                        margin-top: 2.5rem;
                        margin-bottom: 2.5rem;
                        margin-left: 2.5rem;
                        margin-right: 2.5rem;
                    }

                    @media print {
                        #page-number::after {
                            counter-increment: page !important;
                            content: "Halaman " counter(page);
                        }
                    }

                    body, p {
                        font-family: Arial, sans-serif;
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                        border: 1px solid black;
                    }

                    th, td {
                        border: 1px solid black;
                        padding: 6px;
                    }

                    td {
                        vertical-align: top;
                    }

                    th {
                        background-color: #e5e7eb;
                    }

                    tfoot > tr > td {
                        font-weight: bold;
                        background-color: #e5e7eb;
                    }

                    .text-left, .text-start {
                        text-align: left;
                    }

                    .text-center {
                        text-align: center;
                    }

                    .text-right, .text-end {
                        text-align: right;
                    }

                    .text-sm {
                        font-size: 0.875rem;
                    }

                    .text-xs {
                        font-size: 0.75rem;
                    }

                    .text-lg {
                        font-size: 1.125rem;
                    }

                    .text-xl {
                        font-size: 1.25rem;
                    }

                    .font-semibold {
                        font-weight: bold;
                    }

                    .break-all {
                        word-break: break-all;
                    }

                    .mb-4 {
                        margin-bottom: 1rem;
                    }
                </style>
            </head>
            
            <body>
    `;
    content += element.innerHTML;
    content += `
            </body>
        </html>`;

    const response = await reportStore.generatePdf(content);

    // Create blob from base64
    const byteCharacters = atob(response.result.pdf);
    const byteArrays = [];

    for (let i = 0; i < byteCharacters.length; i++) {
        byteArrays.push(byteCharacters.charCodeAt(i));
    }

    const byteArray = new Uint8Array(byteArrays);

    const url = window.URL.createObjectURL(
        new Blob([byteArray], { type: "application/pdf" })
    );

    // Remove iframe if exists
    const oldIframe = document.querySelector("iframe");
    if (oldIframe) {
        oldIframe.remove();
    }

    // Create iframe
    const iframe = document.createElement("iframe");
    iframe.style.display = "none";
    iframe.src = url;
    document.body.appendChild(iframe);

    // Print when iframe is loaded
    iframe.onload = function () {
        iframe.contentWindow.print();
    };

    window.URL.revokeObjectURL(url);
}

function getReportType() {
    if (form.value.report_type === "sales") {
        return "penjualan";
    } else if (form.value.report_type === "purchase") {
        return "pembelian";
    }
}

function validateGetReport() {
    let result = true;

    if (!form.value.report_type) {
        formValidation.value.report_type = "Pilih jenis laporan";
        result = false;
    } else {
        formValidation.value.report_type = "";
    }

    if (!form.value.start_date) {
        formValidation.value.start_date = "Pilih tanggal awal";
        result = false;
    } else {
        formValidation.value.start_date = "";
    }

    if (!form.value.end_date) {
        formValidation.value.end_date = "Pilih tanggal akhir";
        result = false;
    } else {
        formValidation.value.end_date = "";
    }

    return result;
}

watch(
    () => form.value.report_type,
    () => {
        reportStore.getReportStatus = "";
    }
);

onMounted(() => {
    if (!form.value.start_date) {
        form.value.start_date = new Date().toISOString().slice(0, 10);
        form.value.end_date = form.value.start_date;
    }
});
</script>

<template>
    <div class="flex flex-col gap-4">
        <DefaultCard>
            <div class="p-6.5">
                <AlertWarning
                    v-if="reportStore.errorMessage"
                    @close="clearErrorMessage"
                    :description="reportStore.errorMessage"
                    class="mb-6"
                />

                <div class="flex flex-col gap-y-4 xl:flex-row xl:gap-x-4">
                    <SelectGroup
                        v-model="form.report_type"
                        id="report_type"
                        label="Jenis Laporan"
                        placeholder="Pilih Jenis Laporan"
                        :options="[
                            { value: 'sales', text: 'Penjualan' },
                            { value: 'purchase', text: 'Pembelian' },
                        ]"
                        :warning="formValidation.report_type"
                    />

                    <CustomDatePicker
                        v-model="form.start_date"
                        id="start_date"
                        label="Tanggal Awal"
                        placeholder="yyyy-mm-dd"
                        :warning="formValidation.start_date"
                    />

                    <CustomDatePicker
                        v-model="form.end_date"
                        id="end_date"
                        label="Tanggal Akhir"
                        placeholder="yyyy-mm-dd"
                        :warning="formValidation.end_date"
                    />
                </div>

                <div class="flex flex-col mt-6 gap-x-4 gap-y-4 sm:flex-row">
                    <CustomButton
                        @click="getReport"
                        :loading="reportStore.getReportStatus === 'loading'"
                        color="bg-primary"
                        :is-full="true"
                        padding="p-3"
                    >
                        Buat Laporan
                    </CustomButton>

                    <CustomButton
                        v-if="reportStore.getReportStatus == 'success'"
                        @click="printReport"
                        :loading="reportStore.getReportStatus === 'loading'"
                        color="bg-green-600"
                        :is-full="true"
                        padding="p-3"
                    >
                        Cetak
                    </CustomButton>
                </div>
            </div>
        </DefaultCard>

        <DefaultCard
            v-if="reportStore.getReportStatus == 'success'"
            class="flex px-4 py-12 overflow-x-auto md:px-12 md:justify-center"
        >
            <template v-if="form.report_type == 'sales'">
                <section id="pdf-content">
                    <div class="w-[7in] p-8 border bg-white">
                        <div class="flex flex-col gap-1 mb-4 text-center">
                            <h2 class="text-lg font-semibold">
                                Laporan Penjualan
                                {{ userStore.user.store[0].name }}
                            </h2>
                            <p class="text-sm">
                                Alamat:
                                {{ userStore.user.store[0].address ?? "-" }},
                                Telp:
                                {{ userStore.user.store[0].phone ?? "-" }}
                            </p>
                            <p class="text-sm">
                                Dari tanggal
                                <span class="font-semibold">{{
                                    reportStore.data.start_date
                                }}</span>
                                sampai
                                <span class="font-semibold">{{
                                    reportStore.data.end_date
                                }}</span>
                            </p>
                        </div>

                        <SalesReportTable :data="reportStore.data" />

                        <p class="mt-2 text-xs">
                            Dicetak pada
                            <span>{{
                                $formatDate.formatDate(new Date())
                            }}</span>
                        </p>
                    </div>
                </section>
            </template>

            <div v-if="form.report_type == 'purchase'">
                <section id="pdf-content">
                    <div class="w-[7in] p-8 border bg-white">
                        <div class="flex flex-col gap-1 mb-4 text-center">
                            <h2 class="text-lg font-semibold">
                                Laporan Pembelian
                                {{ userStore.user.store[0].name }}
                            </h2>
                            <p class="text-sm">
                                Alamat:
                                {{ userStore.user.store[0].address ?? "-" }},
                                Telp:
                                {{ userStore.user.store[0].phone ?? "-" }}
                            </p>
                            <p class="text-sm">
                                Dari tanggal
                                <span class="font-semibold">{{
                                    reportStore.data.start_date
                                }}</span>
                                sampai
                                <span class="font-semibold">{{
                                    reportStore.data.end_date
                                }}</span>
                            </p>
                        </div>

                        <PurchaseReportTable :data="reportStore.data" />

                        <p class="mt-2 text-xs">
                            Dicetak pada
                            <span>{{
                                $formatDate.formatDate(new Date())
                            }}</span>
                        </p>
                    </div>
                </section>
            </div>
        </DefaultCard>
    </div>
</template>
