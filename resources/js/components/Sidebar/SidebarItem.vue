<script setup lang="ts">
import { useSidebarStore } from "@/stores/sidebar";
import { useDarkModeStore } from "@/stores/darkMode";
import SidebarDropdown from "./SidebarDropdown.vue";
import { onMounted, ref, computed } from "vue";
import { Link } from "@inertiajs/inertia-vue3";

const sidebarStore = useSidebarStore();
const darkModeStore = useDarkModeStore();

const props = defineProps(["item", "index"]);
const currentPage = route().current();

const expanded = ref(null);

interface SidebarItem {
    label: string;
    route: string;
}

const handleItemClick = () => {
    expanded.value = !expanded.value;

    if (props.item.children) {
        return props.item.children.some(
            (child: SidebarItem) => sidebarStore.selected === child.route
        );
    }
};

const isActive = computed(() => {
    return window.location.pathname.includes(props.item.route);
});

onMounted(() => {
    expanded.value = window.location.pathname === props.item.route;
});
</script>

<template>
    <li>
        <Link
            :href="item.children ? '#' : item.route"
            class="group relative flex items-center gap-2.5 rounded-md py-2.5 px-4 text-gray-500 dark:text-bodydark1 duration-100 ease-in-out hover:bg-purple-50 dark:hover:bg-meta-4"
            @click.prevent="handleItemClick"
            :class="{
                'bg-purple-100 dark:bg-meta-4 text-primary dark:!text-secondary':
                    isActive,
            }"
        >
            <span
                v-html="props.item.icon"
                :class="{
                    '[&>svg]:text-primary [&>svg]:fill-primary [&>svg]:dark:text-secondary [&>svg]:dark:fill-secondary':
                        isActive,
                }"
            ></span>

            {{ props.item.label }}

            <svg
                v-if="props.item.children"
                class="absolute -translate-y-1/2 fill-gray-500 dark:fill-gray-300 right-4 top-1/2"
                :class="{ 'rotate-180': expanded }"
                width="20"
                height="20"
                viewBox="0 0 20 20"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M4.41107 6.9107C4.73651 6.58527 5.26414 6.58527 5.58958 6.9107L10.0003 11.3214L14.4111 6.91071C14.7365 6.58527 15.2641 6.58527 15.5896 6.91071C15.915 7.23614 15.915 7.76378 15.5896 8.08922L10.5896 13.0892C10.2641 13.4147 9.73651 13.4147 9.41107 13.0892L4.41107 8.08922C4.08563 7.76378 4.08563 7.23614 4.41107 6.9107Z"
                    fill=""
                />
            </svg>
        </Link>

        <!-- Dropdown Menu Start -->
        <div class="overflow-hidden transform translate" v-show="expanded">
            <SidebarDropdown
                v-if="props.item.children"
                :items="props.item.children"
                :currentPage="currentPage"
                :page="props.item.label"
            />
            <!-- Dropdown Menu End -->
        </div>
    </li>
</template>
