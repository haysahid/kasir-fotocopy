<script setup>
import { useSidebarStore } from "@/stores/sidebar";
import { useUserStore } from "@/stores/user";
import { onClickOutside } from "@vueuse/core";
import { ref, onMounted } from "vue";
import SidebarItem from "./SidebarItem.vue";
import { useConfigStore } from "@/stores/config";
import { Link } from "@inertiajs/inertia-vue3";

const target = ref(null);

const sidebarStore = useSidebarStore();
const userStore = useUserStore();
const configStore = useConfigStore();

const showSidebar = ref(false);

onClickOutside(target, () => {
    sidebarStore.isSidebarOpen = false;
});

onMounted(() => {
    showSidebar.value = true;
});

const menuGroups = ref([
    {
        name: "MENU",
        menuItems: [
            {
                label: "Dashboard",
                route: "/dashboard",
                roles: [4, 5],
                // children: [{ label: "eCommerce", route: "/" }],
                icon: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="w-5 text-gray-500 fill-gray-500 dark:fill-gray-300 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 2C17.523 2 22 6.477 22 12C22 17.523 17.523 22 12 22C6.477 22 2 17.523 2 12C2 6.477 6.477 2 12 2ZM16.596 7.404C16.4971 7.30478 16.3663 7.24366 16.2267 7.2314C16.0871 7.21915 15.9477 7.25654 15.833 7.337C12.943 9.365 11.313 10.567 10.939 10.939C10.658 11.2206 10.5001 11.6022 10.5001 12C10.5001 12.3978 10.658 12.7794 10.939 13.061C11.2206 13.342 11.6022 13.4999 12 13.4999C12.3978 13.4999 12.7794 13.342 13.061 13.061C13.28 12.841 14.479 11.21 16.659 8.164C16.7401 8.05035 16.7783 7.91161 16.7668 7.77245C16.7553 7.63329 16.6947 7.50274 16.596 7.404ZM17.5 11C17.2348 11 16.9804 11.1054 16.7929 11.2929C16.6054 11.4804 16.5 11.7348 16.5 12C16.5 12.2652 16.6054 12.5196 16.7929 12.7071C16.9804 12.8946 17.2348 13 17.5 13C17.7652 13 18.0196 12.8946 18.2071 12.7071C18.3946 12.5196 18.5 12.2652 18.5 12C18.5 11.7348 18.3946 11.4804 18.2071 11.2929C18.0196 11.1054 17.7652 11 17.5 11ZM6.5 11C6.23478 11 5.98043 11.1054 5.79289 11.2929C5.60536 11.4804 5.5 11.7348 5.5 12C5.5 12.2652 5.60536 12.5196 5.79289 12.7071C5.98043 12.8946 6.23478 13 6.5 13C6.76522 13 7.01957 12.8946 7.20711 12.7071C7.39464 12.5196 7.5 12.2652 7.5 12C7.5 11.7348 7.39464 11.4804 7.20711 11.2929C7.01957 11.1054 6.76522 11 6.5 11ZM8.818 7.404C8.72502 7.31116 8.61467 7.23753 8.49324 7.18734C8.37181 7.13714 8.24169 7.11135 8.11029 7.11144C7.9789 7.11154 7.84881 7.13751 7.72745 7.18788C7.60609 7.23825 7.49584 7.31202 7.403 7.405C7.21549 7.59277 7.11026 7.84734 7.11044 8.11271C7.11054 8.2441 7.13651 8.37419 7.18688 8.49555C7.23725 8.61691 7.31102 8.72716 7.404 8.82C7.59177 9.00751 7.84634 9.11274 8.11171 9.11256C8.37707 9.11237 8.63149 9.00677 8.819 8.819C9.00651 8.63123 9.11174 8.37666 9.11156 8.11129C9.11137 7.84593 9.00577 7.59151 8.818 7.404ZM12 5.5C11.7348 5.5 11.4804 5.60536 11.2929 5.79289C11.1054 5.98043 11 6.23478 11 6.5C11 6.76522 11.1054 7.01957 11.2929 7.20711C11.4804 7.39464 11.7348 7.5 12 7.5C12.2652 7.5 12.5196 7.39464 12.7071 7.20711C12.8946 7.01957 13 6.76522 13 6.5C13 6.23478 12.8946 5.98043 12.7071 5.79289C12.5196 5.60536 12.2652 5.5 12 5.5Z"/>
                </svg>`,
            },
            {
                label: "Produk / Jasa",
                route: "/product",
                roles: [4],
                // children: [{ label: "eCommerce", route: "/" }],
                icon: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="w-5 text-gray-500 fill-gray-500 dark:fill-gray-300 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg">
                  <path d="M18 6H16C16 3.79 14.21 2 12 2C9.79 2 8 3.79 8 6H6C4.9 6 4 6.9 4 8V20C4 21.1 4.9 22 6 22H18C19.1 22 20 21.1 20 20V8C20 6.9 19.1 6 18 6ZM10 10C10 10.55 9.55 11 9 11C8.45 11 8 10.55 8 10V8H10V10ZM12 4C13.1 4 14 4.9 14 6H10C10 4.9 10.9 4 12 4ZM16 10C16 10.55 15.55 11 15 11C14.45 11 14 10.55 14 10V8H16V10Z"/>
                </svg>`,
            },
            {
                label: "Pembelian",
                route: "/purchase",
                roles: [4],
                // children: [{ label: "eCommerce", route: "/" }],
                icon: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="w-5 text-gray-500 fill-gray-500 dark:fill-gray-300 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M7.334 7.00001H21C21.1615 7.00054 21.3205 7.04006 21.4634 7.11522C21.6064 7.19037 21.7291 7.29893 21.8211 7.43167C21.913 7.56441 21.9716 7.7174 21.9918 7.87763C22.012 8.03786 21.9932 8.2006 21.937 8.35201L18.937 16.352C18.79 16.741 18.417 17 18 17H10C9.597 17 9.232 16.757 9.077 16.385L4.334 5.00001H2V3.00001H4.333C4.72806 2.99911 5.11448 3.11553 5.4433 3.33451C5.77211 3.55348 6.02851 3.86515 6.18 4.23001L7.334 7.00001ZM11 12L14 15L17 12H15V9.00001H13V12H11ZM10.5 21C11.3284 21 12 20.3284 12 19.5C12 18.6716 11.3284 18 10.5 18C9.67157 18 9 18.6716 9 19.5C9 20.3284 9.67157 21 10.5 21ZM17.5 21C18.3284 21 19 20.3284 19 19.5C19 18.6716 18.3284 18 17.5 18C16.6716 18 16 18.6716 16 19.5C16 20.3284 16.6716 21 17.5 21Z"/>
                </svg>`,
            },
            {
                label: "Penjualan",
                route: "/sales",
                roles: [4, 5],
                // children: [{ label: "eCommerce", route: "/" }],
                icon: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="w-5 text-gray-500 fill-gray-500 dark:fill-gray-300 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M7.334 7.00001H21C21.1615 7.00054 21.3205 7.04006 21.4634 7.11522C21.6064 7.19037 21.7291 7.29893 21.8211 7.43167C21.913 7.56441 21.9716 7.7174 21.9918 7.87763C22.012 8.03786 21.9932 8.2006 21.937 8.35201L18.937 16.352C18.79 16.741 18.417 17 18 17H10C9.597 17 9.232 16.757 9.077 16.385L4.334 5.00001H2V3.00001H4.333C4.72806 2.99911 5.11448 3.11553 5.4433 3.33451C5.77211 3.55348 6.02851 3.86515 6.18 4.23001L7.334 7.00001ZM15 13H17V11H15V9.00001H13V11H11V13H13V15H15V13ZM10.5 21C11.3284 21 12 20.3284 12 19.5C12 18.6716 11.3284 18 10.5 18C9.67157 18 9 18.6716 9 19.5C9 20.3284 9.67157 21 10.5 21ZM17.5 21C18.3284 21 19 20.3284 19 19.5C19 18.6716 18.3284 18 17.5 18C16.6716 18 16 18.6716 16 19.5C16 20.3284 16.6716 21 17.5 21Z"/>
                </svg>`,
            },
            {
                label: "Riwayat Pembelian",
                route: "/purchase-history",
                roles: [4],
                // children: [{ label: "eCommerce", route: "/" }],
                icon: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="w-5 text-gray-500 fill-gray-500 dark:fill-gray-300 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M7.334 7.00001H21C21.1615 7.00054 21.3205 7.04006 21.4634 7.11522C21.6064 7.19037 21.7291 7.29893 21.8211 7.43167C21.913 7.56441 21.9716 7.7174 21.9918 7.87763C22.012 8.03786 21.9932 8.2006 21.937 8.35201L18.937 16.352C18.79 16.741 18.417 17 18 17H10C9.597 17 9.232 16.757 9.077 16.385L4.334 5.00001H2V3.00001H4.333C4.72806 2.99911 5.11448 3.11553 5.4433 3.33451C5.77211 3.55348 6.02851 3.86515 6.18 4.23001L7.334 7.00001ZM11 12L14 15L17 12H15V9.00001H13V12H11ZM10.5 21C11.3284 21 12 20.3284 12 19.5C12 18.6716 11.3284 18 10.5 18C9.67157 18 9 18.6716 9 19.5C9 20.3284 9.67157 21 10.5 21ZM17.5 21C18.3284 21 19 20.3284 19 19.5C19 18.6716 18.3284 18 17.5 18C16.6716 18 16 18.6716 16 19.5C16 20.3284 16.6716 21 17.5 21Z"/>
                </svg>`,
            },
            {
                label: "Riwayat Penjualan",
                route: "/sales-history",
                roles: [4],
                // children: [{ label: "eCommerce", route: "/" }],
                icon: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="w-5 text-gray-500 fill-gray-500 dark:fill-gray-300 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M7.334 7.00001H21C21.1615 7.00054 21.3205 7.04006 21.4634 7.11522C21.6064 7.19037 21.7291 7.29893 21.8211 7.43167C21.913 7.56441 21.9716 7.7174 21.9918 7.87763C22.012 8.03786 21.9932 8.2006 21.937 8.35201L18.937 16.352C18.79 16.741 18.417 17 18 17H10C9.597 17 9.232 16.757 9.077 16.385L4.334 5.00001H2V3.00001H4.333C4.72806 2.99911 5.11448 3.11553 5.4433 3.33451C5.77211 3.55348 6.02851 3.86515 6.18 4.23001L7.334 7.00001ZM15 13H17V11H15V9.00001H13V11H11V13H13V15H15V13ZM10.5 21C11.3284 21 12 20.3284 12 19.5C12 18.6716 11.3284 18 10.5 18C9.67157 18 9 18.6716 9 19.5C9 20.3284 9.67157 21 10.5 21ZM17.5 21C18.3284 21 19 20.3284 19 19.5C19 18.6716 18.3284 18 17.5 18C16.6716 18 16 18.6716 16 19.5C16 20.3284 16.6716 21 17.5 21Z"/>
                </svg>`,
            },
            {
                label: "Laporan",
                route: "/report",
                roles: [4],
                // children: [{ label: "eCommerce", route: "/" }],
                icon: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="w-5 text-gray-500 fill-gray-500 dark:fill-gray-300 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M3.75 1.5675C3.55109 1.5675 3.36032 1.64652 3.21967 1.78717C3.07902 1.92783 3 2.11859 3 2.3175V18.6825C3 18.8814 3.07902 19.0722 3.21967 19.2128C3.36032 19.3535 3.55109 19.4325 3.75 19.4325H17.25C17.4489 19.4325 17.6397 19.3535 17.7803 19.2128C17.921 19.0722 18 18.8814 18 18.6825V8.046C18.0002 7.9453 17.9801 7.84558 17.9409 7.75281C17.9018 7.66003 17.8443 7.5761 17.772 7.50601L11.8665 1.782C11.7268 1.64582 11.5396 1.56942 11.3445 1.569L3.75 1.5675ZM15.399 7.296L12.0945 4.0905V7.296H15.399ZM9 8.25H6V6.75H9V8.25ZM15 12H6V10.5H15V12ZM6 15.75H15V14.25H6V15.75Z"/>
                  <path d="M19.5 11.25V21H6.75V22.5H20.25C20.4489 22.5 20.6397 22.421 20.7803 22.2803C20.921 22.1397 21 21.9489 21 21.75V11.25H19.5Z"/>
                </svg>`,
            },
            {
                label: "Karyawan",
                route: "/employee",
                roles: [4],
                // children: [{ label: "eCommerce", route: "/" }],
                icon: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="w-5 text-gray-500 fill-gray-500 dark:fill-gray-300 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 4.5C12.9283 4.5 13.8185 4.86875 14.4749 5.52513C15.1313 6.1815 15.5 7.07174 15.5 8C15.5 8.92826 15.1313 9.8185 14.4749 10.4749C13.8185 11.1313 12.9283 11.5 12 11.5C11.0717 11.5 10.1815 11.1313 9.52513 10.4749C8.86875 9.8185 8.5 8.92826 8.5 8C8.5 7.07174 8.86875 6.1815 9.52513 5.52513C10.1815 4.86875 11.0717 4.5 12 4.5ZM5 7C5.56 7 6.08 7.15 6.53 7.42C6.38 8.85 6.8 10.27 7.66 11.38C7.16 12.34 6.16 13 5 13C4.20435 13 3.44129 12.6839 2.87868 12.1213C2.31607 11.5587 2 10.7956 2 10C2 9.20435 2.31607 8.44129 2.87868 7.87868C3.44129 7.31607 4.20435 7 5 7ZM19 7C19.7956 7 20.5587 7.31607 21.1213 7.87868C21.6839 8.44129 22 9.20435 22 10C22 10.7956 21.6839 11.5587 21.1213 12.1213C20.5587 12.6839 19.7956 13 19 13C17.84 13 16.84 12.34 16.34 11.38C17.2119 10.2544 17.6166 8.8362 17.47 7.42C17.92 7.15 18.44 7 19 7ZM5.5 17.25C5.5 15.18 8.41 13.5 12 13.5C15.59 13.5 18.5 15.18 18.5 17.25V19H5.5V17.25ZM0 19V17.5C0 16.11 1.89 14.94 4.45 14.6C3.86 15.28 3.5 16.22 3.5 17.25V19H0ZM24 19H20.5V17.25C20.5 16.22 20.14 15.28 19.55 14.6C22.11 14.94 24 16.11 24 17.5V19Z" />
                </svg>`,
            },
            {
                label: "Pengaturan",
                route: "/setting",
                roles: [4],
                // children: [{ label: "eCommerce", route: "/" }],
                icon: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="w-5 text-gray-500 fill-gray-500 dark:fill-gray-300 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg">
                  <path d="M9.25001 22L8.85001 18.8C8.63335 18.7167 8.42935 18.6167 8.23801 18.5C8.04601 18.3833 7.85835 18.2583 7.67501 18.125L4.70001 19.375L1.95001 14.625L4.52501 12.675C4.50835 12.5583 4.50001 12.4457 4.50001 12.337V11.662C4.50001 11.554 4.50835 11.4417 4.52501 11.325L1.95001 9.375L4.70001 4.625L7.67501 5.875C7.85835 5.74167 8.05001 5.61667 8.25001 5.5C8.45001 5.38333 8.65001 5.28333 8.85001 5.2L9.25001 2H14.75L15.15 5.2C15.3667 5.28333 15.571 5.38333 15.763 5.5C15.9543 5.61667 16.1417 5.74167 16.325 5.875L19.3 4.625L22.05 9.375L19.475 11.325C19.4917 11.4417 19.5 11.554 19.5 11.662V12.337C19.5 12.4457 19.4833 12.5583 19.45 12.675L22.025 14.625L19.275 19.375L16.325 18.125C16.1417 18.2583 15.95 18.3833 15.75 18.5C15.55 18.6167 15.35 18.7167 15.15 18.8L14.75 22H9.25001ZM12.05 15.5C13.0167 15.5 13.8417 15.1583 14.525 14.475C15.2083 13.7917 15.55 12.9667 15.55 12C15.55 11.0333 15.2083 10.2083 14.525 9.525C13.8417 8.84167 13.0167 8.5 12.05 8.5C11.0667 8.5 10.2373 8.84167 9.56201 9.525C8.88735 10.2083 8.55001 11.0333 8.55001 12C8.55001 12.9667 8.88735 13.7917 9.56201 14.475C10.2373 15.1583 11.0667 15.5 12.05 15.5Z"/>
                </svg>`,
            },
        ],
    },
]);
</script>

<template>
    <aside
        class="absolute left-0 top-0 z-9999 flex h-screen w-72.5 flex-col overflow-y-hidden duration-300 ease-linear bg-white dark:bg-boxdark lg:static lg:translate-x-0"
        :class="{
            'translate-x-0': sidebarStore.isSidebarOpen,
            '-translate-x-full': !sidebarStore.isSidebarOpen,
        }"
        ref="target"
    >
        <!-- SIDEBAR HEADER -->
        <div class="flex items-center justify-between gap-2 px-6 py-4">
            <Link :href="route('home')">
                <div class="flex flex-row items-center gap-2.5">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.8"
                        stroke="currentColor"
                        class="text-primary dark:!text-secondary size-8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"
                        />
                    </svg>

                    <span
                        class="text-xl font-bold tracking-wide text-gray-900 dark:text-gray-200"
                    >
                        {{ configStore.title }}
                    </span>
                </div>
            </Link>

            <button
                class="block lg:hidden"
                @click="sidebarStore.isSidebarOpen = false"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="2"
                    stroke="currentColor"
                    class="size-5 dark:text-gray-500"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"
                    />
                </svg>
            </button>
        </div>
        <!-- SIDEBAR HEADER -->

        <div
            class="flex flex-col justify-between h-full mt-4 overflow-y-auto duration-300 ease-linear no-scrollbar"
        >
            <!-- Sidebar Menu -->
            <nav class="px-6 py-4">
                <template v-for="menuGroup in menuGroups" :key="menuGroup.name">
                    <div>
                        <h3
                            class="mb-2 ml-4 text-sm font-medium text-bodydark2"
                        >
                            {{ menuGroup.name }}
                        </h3>

                        <ul class="flex flex-col mb-6">
                            <template
                                v-for="(menuItem, index) in menuGroup.menuItems"
                                :key="index"
                            >
                                <SidebarItem
                                    v-if="
                                        menuItem.roles.includes(
                                            userStore.user.role_id
                                        )
                                    "
                                    :item="menuItem"
                                    :index="index"
                                />
                            </template>
                        </ul>
                    </div>
                </template>
            </nav>
            <!-- Sidebar Menu -->

            <!-- <div
                class="w-full px-2 py-2 mx-auto mb-10 text-center border rounded-sm max-w-60 dark:border-strokedark dark:bg-boxdark"
            >
                <p class="text-xs text-slate-500">
                    &copy; Copyright {{ configStore.copyrightYear }}
                    {{ configStore.copyrightName }}.<br />All rights reserved
                </p>
            </div> -->
        </div>
    </aside>
</template>
