<script setup>
import { ref, onUpdated } from "vue";

const props = defineProps([
    "id",
    "label",
    "modelValue",
    "disable",
    "disableMargin",
    "small",
]);
const emit = defineEmits(["update:modelValue", "change"]);

const switcherToggle = ref(props.modelValue);

function updateValue() {
    if (props.disable) return;

    switcherToggle.value = !switcherToggle.value;
    emit("update:modelValue", switcherToggle.value);
    emit("change", switcherToggle.value);
}

onUpdated(() => {
    switcherToggle.value = props.modelValue;
});
</script>

<template>
    <div
        :class="{
            'mb-4.5': !disableMargin,
        }"
    >
        <label
            v-if="props.label"
            class="mb-2.5 block text-black dark:text-gray-200"
        >
            {{ props.label }}
        </label>

        <label
            :for="props.id"
            class="flex items-center h-8 select-none w-14"
            :class="{
                'cursor-pointer': !props.disable,
                '!w-8 !h-5': props.small,
            }"
        >
            <div class="relative">
                <input
                    type="checkbox"
                    :id="props.id"
                    class="sr-only"
                    @change="updateValue"
                />
                <div
                    class="block h-8 w-14 rounded-full bg-meta-9 dark:bg-[#5A616B]"
                    :class="{
                        'bg-primary dark:bg-primary': switcherToggle,
                        '!w-8 !h-5': props.small,
                    }"
                ></div>
                <div
                    :class="{
                        '!w-3 !h-3': props.small,
                        '!right-1 !translate-x-full !bg-white dark:!bg-white':
                            switcherToggle,
                    }"
                    class="absolute w-6 h-6 transition bg-white rounded-full left-1 top-1"
                ></div>
            </div>
        </label>
    </div>
</template>
