<script setup>
const props = defineProps({
    label: {
        type: String,
    },
    autoWrap: {
        type: Boolean,
        default: true,
    },
    showBorderBottom: {
        type: Boolean,
        default: true,
    },
});
</script>

<template>
    <div
        class="flex flex-row items-center justify-between max-sm:py-3 max-sm:gap-y-0.5 duration-300 ease-linear"
        :class="{
            'flex-wrap max-sm:!items-start': props.autoWrap,
            'border-b border-stroke dark:border-strokedark':
                props.showBorderBottom,
        }"
    >
        <p
            class="text-sm text-body sm:w-1/3 dark:text-slate-300 px-1.5 sm:py-2 md:p-3"
        >
            {{ props.label }}
        </p>
        <div class="text-black sm:w-2/3 dark:text-white px-1.5 sm:py-2 md:p-3">
            <slot></slot>
        </div>
    </div>
</template>
