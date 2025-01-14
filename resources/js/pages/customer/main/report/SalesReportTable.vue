<script setup lang="ts">
defineProps<{
    data: Object;
}>();
</script>

<template>
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead>
                <tr
                    class="[&>th]:text-center [&>th]:font-semibold [&>th]:border [&>th]:border-black [&>th]:p-1 [&>th]:bg-gray-200"
                >
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kode Transaksi</th>
                    <th>Nama Barang</th>
                    <th>Qty</th>
                    <th>Satuan</th>
                    <th>Harga Satuan</th>
                    <th>Total Harga</th>
                    <th>Subtotal</th>
                    <th>Status</th>
                    <th>Profit</th>
                </tr>
            </thead>
            <tbody>
                <template
                    v-for="(sales, index) in data.sales"
                    :key="index"
                    class="[&>td]:border [&>td]:border-black"
                >
                    <template v-for="(item, itemIndex) in sales.sales_items">
                        <tr
                            class="[&>td]:border [&>td]:border-black [&>td]:align-top [&>td]:p-1"
                        >
                            <template v-if="itemIndex == 0">
                                <td :rowspan="sales.sales_items.length">
                                    {{ index + 1 }}
                                </td>
                                <td :rowspan="sales.sales_items.length">
                                    {{
                                        $formatDate.formatDate(sales.created_at)
                                    }}
                                </td>
                                <td
                                    :rowspan="sales.sales_items.length"
                                    class="break-all"
                                >
                                    {{ sales.code }}
                                </td>
                            </template>

                            <td>{{ item.product.name }}</td>
                            <td class="text-center">
                                {{ item.quantity }}
                            </td>
                            <td class="text-center">
                                {{ item.product.unit }}
                            </td>
                            <td class="text-end">
                                {{ $formatCurrency(item.item_price) }}
                            </td>
                            <td class="text-end">
                                {{ $formatCurrency(item.sub_total) }}
                            </td>

                            <template v-if="itemIndex == 0">
                                <td
                                    :rowspan="sales.sales_items.length"
                                    class="text-end"
                                >
                                    {{ $formatCurrency(sales.total) }}
                                </td>
                                <td
                                    :rowspan="sales.sales_items.length"
                                    class="text-center"
                                >
                                    {{ sales.status }}
                                </td>
                                <td
                                    :rowspan="sales.sales_items.length"
                                    class="text-end"
                                >
                                    {{ $formatCurrency(sales.profit) }}
                                </td>
                            </template>
                        </tr>
                    </template>
                </template>
            </tbody>
            <tfoot>
                <tr
                    class="[&>td]:border [&>td]:border-black [&>td]:p-1 [&>td]:font-semibold [&>td]:bg-gray-200"
                >
                    <td colspan="10" class="text-end">Total Penjualan</td>
                    <td class="text-end">
                        {{ $formatCurrency(data.total) }}
                    </td>
                </tr>
                <tr
                    class="[&>td]:border [&>td]:border-black [&>td]:p-1 [&>td]:font-semibold [&>td]:bg-gray-200"
                >
                    <td colspan="10" class="text-end">Total Profit</td>
                    <td class="text-end">
                        {{ $formatCurrency(data.total_profit) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</template>
