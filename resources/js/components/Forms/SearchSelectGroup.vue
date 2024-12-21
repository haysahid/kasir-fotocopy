<script setup lang="ts">
import { ref, onMounted, onUpdated, watch, inject, computed } from "vue";
import { onClickOutside } from "@vueuse/core";

interface Option {
    value: string;
    text: string;
    subtext?: string;
    selected: boolean;
    element?: HTMLElement;
}

const debounce = inject("debounce");

const props = defineProps([
    "id",
    "label",
    "placeholder",
    "options",
    "warning",
    "type",
    "modelValue",
]);
const emit = defineEmits(["search", "select", "clear", "update:modelValue"]);

const target = ref(null);
const options = ref<Option[]>();
const selectedOptions = ref<Option[]>([]);
const show = ref(false);

const debounceTimeout = ref(null);

function search(event: InputEvent) {
    window.clearTimeout(debounceTimeout.value);
    debounceTimeout.value = window.setTimeout(() => {
        emit("search", (event.target as HTMLInputElement).value.trim());
    }, 300);
}

const open = () => {
    show.value = true;

    // Auto focus input
    if (props.type == "single") {
        const input = document.getElementById(props.id) as HTMLInputElement;
        input?.focus();
    }

    if (props.type == "multiple") {
        const input = document.getElementById(
            `multiple_${props.id}`
        ) as HTMLInputElement;
        input?.focus();
    }
};

const isOpen = () => {
    return show.value === true;
};

onClickOutside(target, () => {
    show.value = false;
});

onMounted(() => {
    loadOptions();
});

watch(
    () => props.options,
    () => {
        loadOptions();
    }
);

const loadOptions = () => {
    options.value = props.options.map((option) => ({
        value: option.value,
        text: option.text,
        subtext: option.description,
        selected: false,
    }));
};

const select = (indexAndOption, event: MouseEvent) => {
    const index = indexAndOption.index;

    const newOptions = [...options.value];

    if (props.type == "multiple") {
        newOptions[index].selected = !newOptions[index].selected;

        if (newOptions[index].selected) {
            selectedOptions.value.push(newOptions[index]);
        } else {
            selectedOptions.value = selectedOptions.value.filter(
                (option) => option.value !== newOptions[index].value
            );
        }

        options.value = newOptions;

        emit(
            "select",
            selectedOptions.value.map((option) => option.value)
        );

        emit(
            "update:modelValue",
            selectedOptions.value.map((option) => option.value)
        );

        // Auto focus input
        const input = document.getElementById(
            `multiple_${props.id}`
        ) as HTMLInputElement;
        input?.focus();
    } else {
        newOptions[index].selected = !newOptions[index].selected;

        if (newOptions[index].selected) {
            selectedOptions.value = [newOptions[index]];
        }

        newOptions.forEach((option, i) => {
            if (i !== index) {
                option.selected = false;
            }
        });

        options.value = newOptions;

        emit("select", selectedOptions.value[0].value);

        show.value = false;
    }
};

const remove = (option: Option) => {
    const newOptions = [...options.value];

    newOptions.forEach((option) => {
        option.selected = false;
    });

    selectedOptions.value = selectedOptions.value.filter(
        (selectedOption) => selectedOption.value !== option.value
    );

    options.value = newOptions;

    emit(
        "update:modelValue",
        selectedOptions.value.map((option) => option.value)
    );
};

const clearSelectedOptions = () => {
    selectedOptions.value = [];
    options.value.forEach((option) => {
        option.selected = false;
    });
    emit("clear");
    emit("update:modelValue", []);
};

watch(
    () => props.modelValue,
    () => {
        if (props.type == "single") {
            const newOptions = [...options.value];

            newOptions.forEach((option) => {
                option.selected = false;
            });

            const selectedOption = newOptions.find(
                (option) => option.value === props.modelValue
            );

            if (selectedOption) {
                selectedOption.selected = true;
                selectedOptions.value = [selectedOption];
            }

            options.value = newOptions;
        }

        if (props.type == "multiple") {
            const newOptions = [...options.value];

            newOptions.forEach((option) => {
                option.selected = false;
            });

            selectedOptions.value = props.modelValue.map((value) => {
                const selectedOption = newOptions.find(
                    (option) => option.value === value
                );

                if (selectedOption) {
                    selectedOption.selected = true;
                }

                return selectedOption;
            });

            options.value = newOptions;
        }
    }
);

defineExpose({
    clearSelectedOptions,
});
</script>

<template>
    <div class="z-50 w-full">
        <label
            :for="props.id"
            v-if="props.label"
            class="block text-sm font-normal mb-1.5 text-black dark:text-white"
        >
            {{ props.label }}
        </label>

        <div class="relative flex flex-col items-center">
            <!-- Input -->
            <div @click="open" class="relative w-full">
                <!-- Multiple -->
                <div
                    v-if="
                        props.type == 'multiple' && selectedOptions.length > 0
                    "
                    @click="open"
                    class="flex flex-col w-full gap-1 py-2 pl-3 pr-4 text-base text-black duration-300 ease-linear bg-transparent border rounded-lg outline-none border-stroke focus:border-primary focus-visible:shadow-none dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary dark:text-white placeholder:text-gray-400"
                    :class="{
                        '!border-danger focus:!border-danger dark:!border-danger dark:focus:!border-danger':
                            props.warning,
                        'bg-whiten dark:bg-slate-700': props.disabled,
                    }"
                >
                    <input
                        v-if="isOpen()"
                        :id="'multiple_' + props.id"
                        :placeholder="props.placeholder"
                        class="w-full p-0 text-black duration-300 ease-linear bg-transparent border-none rounded-lg !outline-none appearance-none focus:!outline-none stroke-none focus:stroke-none focus-visible:border-none focus:border-none focus:ring-0 focus-visible:shadow-none dark:text-white placeholder:text-gray-400"
                        :class="{
                            '!border-danger focus:!border-danger dark:!border-danger dark:focus:!border-danger':
                                props.warning,
                            'bg-whiten dark:bg-slate-700': props.disabled,
                        }"
                        autocomplete="off"
                        @input="search"
                    />

                    <div class="flex flex-wrap gap-1">
                        <div
                            v-for="(option, i) in selectedOptions"
                            :key="i"
                            class="flex items-center px-2 py-0.5 text-sm rounded-full bg-primary/5 dark:bg-primary/10 w-min text-nowrap"
                        >
                            <span>{{ option.text }}</span>
                            <button
                                @click="remove(option)"
                                type="button"
                                class="p-1 text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="size-5"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M6 18 18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Single -->
                <div
                    v-else-if="
                        props.type == 'single' &&
                        selectedOptions.length > 0 &&
                        !isOpen()
                    "
                    @click="open"
                    class="w-full py-2 pl-3 pr-4 text-base text-black duration-300 ease-linear bg-transparent border rounded-lg outline-none border-stroke focus:border-primary focus-visible:shadow-none dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary dark:text-white placeholder:text-gray-400 overflow-ellipsis"
                    :class="{
                        '!border-danger focus:!border-danger dark:!border-danger dark:focus:!border-danger':
                            props.warning,
                        'bg-whiten dark:bg-slate-700': props.disabled,
                    }"
                >
                    {{ selectedOptions[0].text }}
                </div>

                <input
                    v-else
                    :id="props.id"
                    :placeholder="props.placeholder"
                    class="w-full py-2 pl-3 pr-4 text-black duration-300 ease-linear bg-transparent border rounded-lg outline-none border-stroke focus:border-primary focus-visible:shadow-none dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary dark:text-white placeholder:text-gray-400"
                    :class="{
                        '!border-danger focus:!border-danger dark:!border-danger dark:focus:!border-danger':
                            props.warning,
                        'bg-whiten dark:bg-slate-700': props.disabled,
                    }"
                    autocomplete="off"
                    @input="search"
                />

                <div class="absolute top-[7px] right-2.5">
                    <div class="flex">
                        <button
                            v-if="
                                selectedOptions.length > 0 &&
                                props.type == 'single'
                            "
                            @click="clearSelectedOptions"
                            type="button"
                            class="p-1 text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="size-5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 18 18 6M6 6l12 12"
                                />
                            </svg>
                        </button>

                        <button
                            v-else
                            @click="open"
                            type="button"
                            class="p-1"
                            :class="{
                                'rotate-180': isOpen(),
                            }"
                        >
                            <svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                                class="size-5"
                            >
                                <g opacity="0.8">
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M5.29289 8.29289C5.68342 7.90237 6.31658 7.90237 6.70711 8.29289L12 13.5858L17.2929 8.29289C17.6834 7.90237 18.3166 7.90237 18.7071 8.29289C19.0976 8.68342 19.0976 9.31658 18.7071 9.70711L12.7071 15.7071C12.3166 16.0976 11.6834 16.0976 11.2929 15.7071L5.29289 9.70711C4.90237 9.31658 4.90237 8.68342 5.29289 8.29289Z"
                                        fill="#637381"
                                    ></path>
                                </g>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Options -->
            <div v-if="isOpen()" class="w-full px-4">
                <div
                    ref="target"
                    class="absolute left-0 z-40 w-full mt-1 overflow-y-auto bg-white rounded shadow max-h-select top-full dark:bg-form-input"
                >
                    <div class="flex flex-col w-full">
                        <template v-for="(option, index) in options">
                            <div
                                v-if="
                                    !selectedOptions
                                        .map((option) => option.value)
                                        .includes(option.value)
                                "
                                :key="index"
                                @click="
                                    select(
                                        {
                                            index: index,
                                            option: option,
                                        },
                                        $event
                                    )
                                "
                                class="w-full rounded-t cursor-pointer border-stroke hover:bg-primary/5 dark:hover:bg-secondary/5 dark:border-form-strokedark"
                            >
                                <div
                                    :class="[
                                        'relative flex w-full items-center border-l-2 border-transparent p-2 pl-2',
                                    ]"
                                >
                                    <div class="flex flex-col w-full">
                                        <div
                                            class="mx-2 leading-6 dark:text-white"
                                            :class="{
                                                'font-bold': option.selected,
                                            }"
                                        >
                                            {{ option.text }}
                                        </div>
                                        <p
                                            v-if="option.subtext"
                                            class="mx-2 text-sm leading-6 text-body"
                                        >
                                            {{ option.subtext }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <p v-if="props.warning" class="mt-1 text-sm text-danger">
            {{ props.warning }}
        </p>
    </div>
</template>
