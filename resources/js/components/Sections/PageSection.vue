<script setup>
const props = defineProps({
    pageTitle: String,
});
</script>

<template>
    <div class="flex flex-row items-center justify-between gap-3">
        <h2 class="font-medium text-black text-title-sm2 dark:text-white">
            {{ props.pageTitle }}
        </h2>

        <slot> </slot>
    </div>
</template>
