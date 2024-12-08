<script setup>
import CircularLoading from "@/components/CircularLoading.vue";

const props = defineProps({
    type: {
        type: String,
        default: "button",
    },
    loading: {
        type: Boolean,
        default: false,
    },
    loadingText: {
        type: String,
    },
    enable: {
        type: Boolean,
        default: true,
    },
    color: {
        type: String,
        default: "bg-primary",
    },
    isFull: {
        type: Boolean,
        default: true,
    },
    padding: {
        type: String,
        default: "py-3 px-4",
    },
    margin: {
        type: String,
        default: "mt-4",
    },
    customClass: {
        type: String,
    },
});

const emit = defineEmits(["click"]);

function click() {
    if (props.enable && !props.loading) {
        emit("click");
    }
}
</script>

<template>
    <button
        @click="click"
        :type="props.type"
        class="flex items-center justify-center font-medium text-gray-100 rounded-full hover:bg-opacity-90"
        :class="[
            props.color,
            props.padding,
            props.margin,
            props.customClass,
            {
                'w-full': props.isFull,
                'bg-slate-300 hover:bg-slate-300 !text-slate-400 dark:bg-slate-600 pointer-events-none':
                    !props.enable,
                'pointer-events-none': props.loading,
            },
        ]"
    >
        <template v-if="props.loading">
            <CircularLoading class="w-6 p-1 bg-white" />
            <span v-if="props.loadingText" class="ml-2">{{
                props.loadingText
            }}</span>
        </template>

        <slot v-else></slot>
    </button>
</template>
