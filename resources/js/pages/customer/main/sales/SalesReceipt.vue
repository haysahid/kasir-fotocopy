<script setup lang="ts">
const props = defineProps<{
    store: Object;
    sales: Object;
    paperSize: string;
}>();
</script>

<template>
    <div
        class="px-4 py-6 duration-100 ease-in-out bg-white border border-gray-300 rounded-sm dark:bg-white"
        :class="[`w-[${props.paperSize}]`]"
    >
        <!-- Header -->
        <div class="mb-4 text-center">
            <p class="">
                <span class="text-xl font-bold">
                    {{ props.store.name }}
                </span>
                <br />
                <span class="text-base">
                    Alamat:
                    {{ props.store.address ?? "-" }}
                </span>
                <br />
                <span class="text-base">
                    Telp:
                    {{ props.store.phone ?? "-" }}
                </span>
            </p>
        </div>

        <!-- Body -->
        <table class="w-full text-base">
            <tbody>
                <tr>
                    <td colspan="2" class="!py-2">
                        <hr class="border-t border-black border-dashed" />
                    </td>
                </tr>

                <tr>
                    <td class="text-left align-top">Kode</td>
                    <td class="text-right break-all align-top">
                        {{ props.sales.code }}
                    </td>
                </tr>
                <tr>
                    <td class="text-left align-top">Tanggal</td>
                    <td class="text-right align-top">
                        {{ $formatDate.formatDate(props.sales.created_at) }}
                    </td>
                </tr>
                <tr>
                    <td class="text-left align-top">Status</td>
                    <td class="text-right align-top">
                        {{ props.sales.status }}
                    </td>
                </tr>
                <tr>
                    <td class="text-left align-top">Catatan</td>
                    <td class="text-right align-top">
                        {{ props.sales.note ?? "-" }}
                    </td>
                </tr>
            </tbody>
        </table>

        <table v-if="props.sales?.sales_items" class="w-full text-base">
            <tbody>
                <tr>
                    <td colspan="2" class="py-2">
                        <hr class="border-t border-black border-dashed" />
                    </td>
                </tr>

                <tr v-for="item in props.sales.sales_items">
                    <td class="py-1 align-bottom">
                        <span class="font-semibold">
                            {{ item.product.name }}
                        </span>
                        <br />
                        <span>
                            {{ $formatCurrency(item.item_price) }} x
                            {{ item.quantity }}
                            {{ item.product.unit }}
                        </span>
                    </td>
                    <td class="py-1 text-right align-bottom">
                        {{ $formatCurrency(item.sub_total) }}
                    </td>
                </tr>

                <tr>
                    <td colspan="2" class="!py-2">
                        <hr class="border-t border-black border-dashed" />
                    </td>
                </tr>

                <tr>
                    <td class="font-semibold text-left">Total</td>
                    <td class="font-semibold text-right">
                        {{ $formatCurrency(props.sales.total) }}
                    </td>
                </tr>
                <tr>
                    <td class="text-left">Bayar</td>
                    <td class="text-right">
                        {{ $formatCurrency(props.sales.payment) }}
                    </td>
                </tr>
                <tr>
                    <td class="text-left">Kembali</td>
                    <td class="text-right">
                        {{ $formatCurrency(props.sales.return) }}
                    </td>
                </tr>

                <tr>
                    <td colspan="2" class="!py-2">
                        <hr class="border-t border-black border-dashed" />
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Footer -->
        <div class="text-center">
            <p class="text-base">Terima kasih!</p>
        </div>
    </div>
</template>

<style scoped>
*,
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
