<script setup>
import { computed, ref } from "vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import CustomButton from "@/components/Forms/CustomButton.vue";
import { useUserStore } from "@/stores/user";
import { useSalesStore } from "@/stores/sales";
import { useVueToPrint } from "vue-to-print";
import SelectGroup from "@/components/Forms/SelectGroup.vue";
import SalesReceipt from "./SalesReceipt.vue";

const props = defineProps({
    showCloseButton: Boolean,
    sales: {
        type: Object,
    },
});
const emit = defineEmits(["close"]);

const userStore = useUserStore();
const salesStore = useSalesStore();

const form = ref({
    paperSize: "57mm",
});

const paperSizeOptions = [
    { text: "57mm", value: "57mm" },
    { text: "58mm", value: "58mm" },
    { text: "80mm", value: "80mm" },
];

const receipt = ref();

function close(value) {
    emit("close", value);
}

const { handlePrint } = useVueToPrint({
    element: receipt,
    documentTitle: "AwesomeFileName",
});

async function print() {
    const element = receipt.value;

    let content = `<html>
            <head>
                <title>${props.sales.code}</title>
                <style>
                    @media print {
                        @page {
                            size: ${form.paperSize};
                            size: portrait;
                            margin: 0 0;
                        }
                    }

                    @page {
                        // size: ${form.paperSize};
                        // size: portrait;
                        margin: 0 0;
                    }

                    body {
                        font-family: "Arial", sans-serif;
                        margin: 0 0,
                    }

                    div,
                    p,
                    span {
                        font-family: Arial, sans-serif !important;
                        line-height: 1rem;
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    .text-left,
                    .text-start {
                        text-align: left;
                    }

                    .text-right,
                    text-end {
                        text-align: right;
                    }

                    .text-center {
                        text-align: center;
                    }

                    .text-nowrap {
                        white-space: nowrap;
                    }

                    .text-base {
                        font-size: 14px;
                    }

                    .text-xl {
                        font-size: 20px;
                    }

                    .font-bold {
                        font-weight: bold;
                    }

                    .font-semibold {
                        font-weight: bold;
                    }

                    .border-black {
                        border-color: #000;
                    }

                    .border-dashed {
                        border-style: dashed;
                    }

                    .mb-4 {
                        margin-bottom: 1rem;
                    }

                    .py-1 {
                        padding-top: 0.25rem;
                        padding-bottom: 0.25rem;
                    }

                    .py-2 {
                        padding-top: 0.5rem;
                        padding-bottom: 0.5rem;
                    }

                    .px-4 {
                        padding-left: 1rem;
                        padding-right: 1rem;
                    }

                    .py-6 {
                        padding-top: 1.5rem;
                        padding-bottom: 1.5rem;
                    }

                    .bg-white {
                        background-color: #fff;
                    }

                    .w-57mm {
                        width: 57mm;
                    }

                    .w-58mm {
                        width: 58mm;
                    }

                    .w-80mm {
                        width: 80mm;
                    }

                    .w-full {
                        width: 100%;
                    }

                    .break-all {
                        word-break: break-all;
                    }

                    .align-top {
                        vertical-align: top;
                    }

                    .align-bottom {
                        vertical-align: bottom;
                    }

                    .border-t {
                        border-top-width: 1px;
                        border-left-width: 0;
                        border-right-width: 0;
                        border-bottom-width: 0;
                    }
                </style>
            </head>
            <body >`;
    content += element.innerHTML;
    content += `
            </body>
        </html>`;

    const response = await salesStore.generateReceipt(
        content,
        form.value.paperSize
    );

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

    // Close modal
    close(true);
}
</script>

<template>
    <DefaultCard
        :show-close-button="props.showCloseButton"
        @close="close(false)"
        class="w-full"
    >
        <div
            v-if="props.sales"
            class="flex flex-col items-center gap-4 px-8 py-8"
        >
            <div class="flex flex-col items-center">
                <h2
                    class="mb-4 text-center text-gray-700 text-title-lg dark:text-gray-200"
                >
                    Cetak Struk
                </h2>
            </div>

            <div class="flex flex-col-reverse w-full gap-8 sm:flex-row">
                <div
                    ref="receipt"
                    class="flex items-center justify-center w-full p-8 bg-gray-200 rounded-lg"
                >
                    <SalesReceipt
                        :store="userStore.user.store[0]"
                        :sales="props.sales"
                        :paperSize="form.paperSize"
                    />
                </div>

                <div class="w-full lg:w-[300px]">
                    <div class="w-full">
                        <SelectGroup
                            v-model="form.paperSize"
                            :options="paperSizeOptions"
                            label="Ukuran Kertas"
                            placeholder="Pilih ukuran kertas"
                        />
                    </div>

                    <div
                        class="flex flex-col-reverse w-full mt-4 gap-x-2 gap-y-2 sm:flex-row"
                    >
                        <CustomButton
                            @click="close(false)"
                            color="bg-slate-400"
                            :is-full="true"
                            padding="py-2.5 px-12"
                        >
                            Batal
                        </CustomButton>
                        <CustomButton
                            @click="print"
                            color="bg-primary"
                            :is-full="true"
                            padding="py-2.5 px-12"
                        >
                            Cetak
                        </CustomButton>
                    </div>
                </div>
            </div>
        </div>
    </DefaultCard>
</template>
