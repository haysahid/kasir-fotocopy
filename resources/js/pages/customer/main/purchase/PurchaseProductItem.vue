<script setup>
const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    isSelected: {
        type: Boolean,
        default: false,
    },
    isInCart: {
        type: Boolean,
        default: false,
    },
    inCartQuantity: {
        type: Number,
        default: 0,
    },
});
</script>

<template>
    <div
        class="flex items-start gap-4"
        :class="{
            '!flex-col !gap-0': props.isInCart,
        }"
    >
        <div v-if="!props.isInCart" class="relative">
            <img
                v-if="props.product.product_images.length > 0"
                :src="props.product.product_images[0].image"
                alt="product"
                class="object-cover w-full rounded-lg size-16"
            />
            <div
                v-else
                class="flex items-center justify-center duration-300 ease-linear bg-gray-100 rounded-lg dark:bg-slate-700 size-16"
                :class="{
                    'dark:!bg-slate-600': props.isSelected,
                }"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="text-gray-500 size-6"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"
                    />
                </svg>
            </div>

            <p
                class="absolute text-xs text-white dark:text-white text-nowrap bottom-0 left-0 bg-secondary dark:bg-secondary dark:bg-opacity-15 rounded-b-lg px-1.5 py-0.5 w-full text-center duration-300 ease-linear"
                :class="{
                    '!bg-primary dark:!bg-secondary dark:!text-slate-700':
                        props.inCartQuantity > 0,
                }"
            >
                {{ props.product.stock - props.inCartQuantity }}
            </p>
        </div>

        <div class="flex flex-col w-full gap-1">
            <h4 class="text-sm font-semibold text-black dark:text-gray-300">
                {{ props.product.name }}
            </h4>

            <p
                v-if="!props.isInCart"
                class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 text-ellipsis"
                :class="{
                    '!text-slate-500': props.inCartQuantity > 0,
                }"
            >
                {{ props.product.description }}
            </p>

            <div
                v-if="!props.isInCart"
                class="self-start px-1 py-0 text-xs text-gray-400 duration-300 ease-linear bg-gray-100 rounded-md dark:bg-gray-600 dark:text-gray-400"
                :class="{
                    '!bg-white !bg-opacity-40 !text-slate-400 dark:!text-slate-400 dark:!bg-opacity-15':
                        props.inCartQuantity > 0,
                }"
            >
                {{ props.product.category }}
            </div>
        </div>

        <div
            class="flex flex-col items-end"
            :class="{
                '!flex-row gap-1 items-center': props.isInCart,
            }"
        >
            <p
                class="text-sm font-semibold text-primary dark:text-secondary text-nowrap"
            >
                Rp {{ $formatCurrency(props.product.purchase_price) }}
            </p>
            <p
                class="text-xs text-gray-500 dark:text-gray-500 text-nowrap"
                :class="{
                    '!text-slate-500': props.inCartQuantity > 0,
                }"
            >
                / {{ props.product.unit }}
            </p>
        </div>
    </div>
</template>
