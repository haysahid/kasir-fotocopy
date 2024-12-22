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
        class="flex flex-row items-center justify-between py-2.5 max-sm:gap-y-0.5 duration-300 ease-linear"
        :class="{
            'flex-wrap max-sm:!items-start': props.autoWrap,
            'border-b border-stroke dark:border-strokedark':
                props.showBorderBottom,
        }"
    >
        <p class="text-sm text-body sm:w-1/3 dark:text-slate-300">
            {{ props.label }}
        </p>
        <div class="text-sm text-gray-900 sm:w-2/3 dark:text-gray-200">
            <slot></slot>
        </div>
    </div>
</template>
