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
    priceToShow: {
        type: String,
        default: "selling_price",
    },
});
</script>

<template>
    <div
        class="flex items-center gap-4 group"
        :class="{
            '!flex-col !gap-0': props.isInCart,
        }"
    >
        <div v-if="!props.isInCart" class="relative">
            <div v-if="props.product.product_images.length > 0" class="size-32">
                <img
                    :src="$getImageUrl(props.product.product_images[0].image)"
                    alt="product"
                    class="object-cover w-full h-full rounded-lg"
                />
            </div>

            <div
                v-else
                class="flex items-center justify-center duration-300 ease-linear bg-gray-100 rounded-lg dark:bg-slate-700 size-32 dark:group-hover:bg-slate-600"
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
                    class="text-gray-400 dark:text-gray-500 size-6"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"
                    />
                </svg>
            </div>

            <p
                class="absolute text-xs text-gray-200 dark:text-gray-200 text-nowrap bottom-1 left-1 bg-gray-800/60 dark:bg-gray-800/60 dark:bg-opacity-15 rounded-md px-1.5 py-0.5 text-center duration-300 ease-linear"
                :class="{
                    '!bg-primary dark:!bg-secondary dark:!text-slate-700':
                        props.inCartQuantity > 0,
                }"
            >
                {{
                    props.priceToShow == "purchase_price"
                        ? props.product.stock + props.inCartQuantity
                        : props.product.stock - props.inCartQuantity
                }}
            </p>
        </div>

        <div
            class="flex flex-col w-full gap-2"
            :class="{
                '!gap-0': props.isInCart,
            }"
        >
            <div class="flex flex-col w-full gap-1">
                <h4
                    class="font-semibold text-gray-900 text-md dark:text-gray-300"
                    :class="{
                        '!text-sm': props.isInCart,
                    }"
                >
                    {{ props.product.name }}
                </h4>

                <p
                    v-if="!props.isInCart"
                    class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 text-ellipsis"
                    :class="{
                        '!text-slate-500 dark:!text-slate-400':
                            props.inCartQuantity > 0,
                    }"
                >
                    {{ props.product.description }}
                </p>

                <div
                    v-if="!props.isInCart"
                    class="self-start px-1.5 py-0 text-xs text-gray-400 duration-300 ease-linear bg-gray-100 rounded-md dark:bg-gray-600 dark:text-gray-400"
                    :class="{
                        '!bg-white !bg-opacity-40 !text-slate-500 dark:!text-slate-400 dark:!bg-opacity-15':
                            props.inCartQuantity > 0,
                    }"
                >
                    {{ props.product.category }}
                </div>
            </div>

            <div
                class="flex flex-row items-center gap-1"
                :class="{
                    '!flex-row gap-1 items-center': props.isInCart,
                }"
            >
                <p
                    class="font-semibold text-md text-primary dark:text-secondary text-nowrap"
                    :class="{
                        '!text-sm': props.isInCart,
                    }"
                >
                    Rp
                    {{
                        props.priceToShow == "purchase_price"
                            ? $formatCurrency(props.product.purchase_price)
                            : $formatCurrency(props.product.selling_price)
                    }}
                </p>
                <p
                    class="text-xs text-gray-500 dark:text-gray-400 text-nowrap"
                    :class="{
                        '!text-slate-500 dark:!text-slate-400':
                            props.inCartQuantity > 0,
                    }"
                >
                    / {{ props.product.unit }}
                </p>
            </div>
        </div>
    </div>
</template>
