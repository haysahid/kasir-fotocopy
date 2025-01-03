<script setup>
import { computed } from "vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import CustomButton from "@/components/Forms/CustomButton.vue";
import { useSalesStore } from "@/stores/sales";

const props = defineProps({
    showCloseButton: Boolean,
    sales: {
        type: Object,
    },
});
const emit = defineEmits(["close"]);

function close(value) {
    emit("close", value);
}

function printReceipt() {
    // TODO: Print receipt
}
</script>

<template>
    <DefaultCard
        :show-close-button="props.showCloseButton"
        @close="close(false)"
        class="w-full"
    >
        <div v-if="props.sales" class="px-6.5 py-8 flex flex-col items-center">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="mb-4 text-primary size-20"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"
                />
            </svg>

            <h2
                class="mb-4 text-center text-gray-700 text-title-lg dark:text-gray-200"
            >
                Struk Penjualan
            </h2>

            <table v-if="props.sales?.sales_items" class="w-full">
                <thead>
                    <tr>
                        <th class="text-left">Nama Barang</th>
                        <th class="text-right">Harga</th>
                        <th class="text-right">Jumlah</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in props.sales.sales_items">
                        <td>{{ item.product.name }}</td>
                        <td class="text-right">
                            {{ $formatCurrency(item.item_price) }}
                        </td>
                        <td class="text-right">{{ item.quantity }}</td>
                        <td class="text-right">
                            {{ $formatCurrency(item.sub_total) }}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="pt-4 text-left">Total</td>
                        <td class="pt-4 text-right">
                            {{ $formatCurrency(props.sales.total) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-left">Bayar</td>
                        <td class="text-right">
                            {{ $formatCurrency(props.sales.payment) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-left">Kembalian</td>
                        <td class="text-right">
                            {{ $formatCurrency(props.sales.return) }}
                        </td>
                    </tr>
                </tfoot>
            </table>

            <div
                class="flex flex-col-reverse w-full mt-4 gap-x-2 gap-y-2 sm:flex-row"
            >
                <CustomButton
                    @click="close(false)"
                    color="bg-slate-400"
                    :is-full="true"
                    padding="py-2.5 px-6"
                >
                    Batal
                </CustomButton>
                <CustomButton
                    @click="printReceipt"
                    color="bg-primary"
                    :is-full="true"
                    padding="py-2.5 px-6"
                >
                    Print
                </CustomButton>
            </div>
        </div>
    </DefaultCard>
</template>
