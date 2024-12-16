<script setup>
const props = defineProps({
    title: String,
    description: String,
    price: Number,
});
</script>

<template>
    <div class="flex flex-col gap-2 p-4">
        <div>
            <h2 class="text-xl font-bold dark:text-white">{{ props.title }}</h2>
            <p class="text-gray-500">{{ props.description }}</p>
        </div>
        <div>
            <span class="text-xl font-bold text-primary dark:text-secondary">
                Rp {{ $formatCurrency(props.price) }}
            </span>
            <span class="dark:text-gray-300"> /bulan</span>
        </div>
    </div>
</template>
