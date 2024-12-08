<script setup lang="ts">
import { ref, onMounted, onUpdated, watch, inject } from "vue";
import { onClickOutside } from "@vueuse/core";

interface Option {
  value: string;
  text: string;
  subtext?: string;
  selected: boolean;
  element?: HTMLElement;
}

const debounce = inject("debounce");

const props = defineProps(["id", "label", "placeholder", "options", "warning"]);
const emit = defineEmits(["search", "select"]);

const target = ref(null);
const options = ref<Option[]>();
const selected = ref<number[]>([]);
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
    value: option,
    text: option.nama,
    subtext: option.ket,
    selected: false,
  }));
};

const select = (indexAndOption, event: MouseEvent) => {
  emit("select", indexAndOption.option.value);

  const index = indexAndOption.index;

  const newOptions = [...options.value];
  if (!newOptions[index].selected) {
    newOptions[index].selected = true;
    newOptions[index].element = event.target as HTMLElement;
    selected.value = [...selected.value, index];
  } else {
    newOptions[index].selected = false;
    selected.value = selected.value.filter((i) => i !== index);
  }
  options.value = newOptions;
};

const remove = (index: number) => {
  const newOptions = [...options.value];
  if (selected.value.includes(index)) {
    newOptions[index].selected = false;
    selected.value = selected.value.filter((i) => i !== index);
    options.value = newOptions;
  }
};

const selectedValues = () => {
  return selected.value.map((option) => options.value[option].value);
};
</script>

<template>
  <div class="relative z-50 w-full mb-4">
    <label
      :for="props.id"
      v-if="props.label"
      class="block mb-2.5 text-black dark:text-white"
    >
      {{ props.label }}
    </label>

    <div>
      <div class="flex flex-col items-center">
        <input name="values" type="hidden" :value="selectedValues()" />
        <div class="relative z-20 inline-block w-full">
          <div class="relative flex flex-col items-center">
            <div @click="open" class="w-full">
              <div
                class="flex py-3 pl-3 pr-3 transition border rounded-lg outline-none border-stroke focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input"
                :class="{
                  '!border-danger focus:border-danger dark:!border-danger dark:focus:border-danger':
                    props.warning,
                }"
              >
                <div class="flex flex-wrap flex-auto gap-3">
                  <div v-show="selected.length === 0" class="flex-1">
                    <input
                      :id="props.id"
                      :placeholder="props.placeholder || 'Select an option'"
                      class="w-full h-full p-1 px-2 bg-transparent outline-none appearance-none"
                      @input="search"
                    />
                  </div>
                </div>
                <div class="flex items-center w-8 py-1 pl-1 pr-1">
                  <button
                    @click="open"
                    type="button"
                    class="w-6 h-6 outline-none cursor-pointer focus:outline-none"
                    :class="isOpen() ? 'rotate-180' : ''"
                  >
                    <svg
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
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
            <div class="w-full px-4">
              <div
                v-show="isOpen()"
                ref="target"
                class="absolute left-0 z-40 w-full overflow-y-auto bg-white rounded shadow max-h-select top-full dark:bg-form-input"
              >
                <div class="flex flex-col w-full">
                  <div v-for="(option, index) in options" :key="index">
                    <div
                      @click="select({ index: index, option: option }, $event)"
                      class="w-full border-b rounded-t cursor-pointer border-stroke hover:bg-primary/5 dark:border-form-strokedark"
                    >
                      <div
                        :class="[
                          'relative flex w-full items-center border-l-2 border-transparent p-2 pl-2',
                          option.selected ? 'border-primary' : '',
                        ]"
                      >
                        <div class="flex flex-col w-full">
                          <div class="mx-2 leading-6 dark:text-white">
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
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <p v-if="props.warning" class="mt-1 text-sm text-danger">
      {{ props.warning }}
    </p>
  </div>
</template>
