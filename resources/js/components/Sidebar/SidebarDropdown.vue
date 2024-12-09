<script setup lang="ts">
import { useSidebarStore } from "@/stores/sidebar";
import { ref } from "vue";
import { Link } from "@inertiajs/inertia-vue3";

const sidebarStore = useSidebarStore();

const props = defineProps(["items", "page"]);
const items = ref(props.items);

const handleItemClick = (index: number) => {
    // const pageName =
    //   sidebarStore.selected === props.items[index].label ? '' : props.items[index].label
    // sidebarStore.selected = pageName
};
</script>

<template>
    <ul class="mt-4 mb-5.5 flex flex-col gap-2.5 pl-6">
        <template v-for="(childItem, index) in items" :key="index">
            <li>
                <Link
                    :href="childItem.route"
                    @click="handleItemClick(index)"
                    class="group relative flex items-center gap-2.5 rounded-md px-4 font-medium text-bodydark2 duration-300 ease-in-out hover:text-white"
                    :class="{
                        '!text-white':
                            childItem.route === sidebarStore.selected,
                    }"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        class="size-5"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M4 10a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H4.75A.75.75 0 0 1 4 10Z"
                            clip-rule="evenodd"
                        />
                    </svg>

                    {{ childItem.label }}
                </Link>
            </li>
        </template>
    </ul>
</template>
