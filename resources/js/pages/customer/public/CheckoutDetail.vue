<script setup>
const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
    total: {
        type: Number,
        required: true,
    },
    amount: {
        type: Number,
        required: true,
    },
});
</script>

<template>
    <div class="flex flex-col gap-1">
        <div
            v-for="item in props.items"
            class="flex items-center justify-between"
        >
            <p class="text-gray-500 dark:text-gray-400">
                {{ item.label }}
            </p>
            <p class="text-gray-500 dark:text-gray-400">
                {{ item.value }}
            </p>
        </div>

        <div class="flex items-center justify-between">
            <p class="text-gray-500 dark:text-gray-400">Jumlah</p>
            <p class="text-gray-500 dark:text-gray-400">
                Rp {{ $formatCurrency(props.amount) }}
            </p>
        </div>

        <div class="flex items-center justify-between">
            <p class="text-gray-500 dark:text-gray-400">PPN (12%)</p>
            <p class="text-gray-500 dark:text-gray-400">
                Rp {{ $formatCurrency(props.amount * 0.11) }}
            </p>
        </div>

        <div class="flex items-center justify-between">
            <p class="text-gray-500 dark:text-gray-400">Total</p>
            <p class="text-xl font-bold text-primary dark:text-secondary">
                Rp
                {{ $formatCurrency(props.total) }}
            </p>
        </div>
    </div>
</template>
