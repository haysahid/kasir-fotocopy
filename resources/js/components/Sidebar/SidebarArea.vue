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
                icon: `<svg
                  class="w-5 fill-current"
                  width="18"
                  height="18"
                  viewBox="0 0 18 18"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M6.10322 0.956299H2.53135C1.5751 0.956299 0.787598 1.7438 0.787598 2.70005V6.27192C0.787598 7.22817 1.5751 8.01567 2.53135 8.01567H6.10322C7.05947 8.01567 7.84697 7.22817 7.84697 6.27192V2.72817C7.8751 1.7438 7.0876 0.956299 6.10322 0.956299ZM6.60947 6.30005C6.60947 6.5813 6.38447 6.8063 6.10322 6.8063H2.53135C2.2501 6.8063 2.0251 6.5813 2.0251 6.30005V2.72817C2.0251 2.44692 2.2501 2.22192 2.53135 2.22192H6.10322C6.38447 2.22192 6.60947 2.44692 6.60947 2.72817V6.30005Z"
                    fill=""
                  />
                  <path
                    d="M15.4689 0.956299H11.8971C10.9408 0.956299 10.1533 1.7438 10.1533 2.70005V6.27192C10.1533 7.22817 10.9408 8.01567 11.8971 8.01567H15.4689C16.4252 8.01567 17.2127 7.22817 17.2127 6.27192V2.72817C17.2127 1.7438 16.4252 0.956299 15.4689 0.956299ZM15.9752 6.30005C15.9752 6.5813 15.7502 6.8063 15.4689 6.8063H11.8971C11.6158 6.8063 11.3908 6.5813 11.3908 6.30005V2.72817C11.3908 2.44692 11.6158 2.22192 11.8971 2.22192H15.4689C15.7502 2.22192 15.9752 2.44692 15.9752 2.72817V6.30005Z"
                    fill=""
                  />
                  <path
                    d="M6.10322 9.92822H2.53135C1.5751 9.92822 0.787598 10.7157 0.787598 11.672V15.2438C0.787598 16.2001 1.5751 16.9876 2.53135 16.9876H6.10322C7.05947 16.9876 7.84697 16.2001 7.84697 15.2438V11.7001C7.8751 10.7157 7.0876 9.92822 6.10322 9.92822ZM6.60947 15.272C6.60947 15.5532 6.38447 15.7782 6.10322 15.7782H2.53135C2.2501 15.7782 2.0251 15.5532 2.0251 15.272V11.7001C2.0251 11.4188 2.2501 11.1938 2.53135 11.1938H6.10322C6.38447 11.1938 6.60947 11.4188 6.60947 11.7001V15.272Z"
                    fill=""
                  />
                  <path
                    d="M15.4689 9.92822H11.8971C10.9408 9.92822 10.1533 10.7157 10.1533 11.672V15.2438C10.1533 16.2001 10.9408 16.9876 11.8971 16.9876H15.4689C16.4252 16.9876 17.2127 16.2001 17.2127 15.2438V11.7001C17.2127 10.7157 16.4252 9.92822 15.4689 9.92822ZM15.9752 15.272C15.9752 15.5532 15.7502 15.7782 15.4689 15.7782H11.8971C11.6158 15.7782 11.3908 15.5532 11.3908 15.272V11.7001C11.3908 11.4188 11.6158 11.1938 11.8971 11.1938H15.4689C15.7502 11.1938 15.9752 11.4188 15.9752 11.7001V15.272Z"
                    fill=""
                  />
                </svg>`,
                label: "Home",
                route: "/home",
                roles: [1, 2, 3, 4, 5],
                // children: [{ label: "eCommerce", route: "/" }],
            },
            {
                icon: `<svg
                class="w-5 fill-current"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"
                />
              </svg>`,
                label: "Referensi",
                route: "/referensi",
                roles: [1, 2, 3],
                children: [
                    //{ label: "Item", route: "/referensi/items" },
                    { label: "Tahun Anggaran", route: "/referensi/yearang" },
                    { label: "Tipe Dokumen", route: "/referensi/tipedokumen" },
                    { label: "Tahapan", route: "/referensi/tahapans" },
                    { label: "Urusan", route: "/referensi/urusan" },
                    { label: "Bidang", route: "/referensi/bidang" },
                    { label: "OPD", route: "/referensi/opd" },
                ],
            },
            {
                icon: `<svg
                  class="w-5 fill-current"
                  width="18"
                  height="18"
                  viewBox="0 0 18 18"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M6.10322 0.956299H2.53135C1.5751 0.956299 0.787598 1.7438 0.787598 2.70005V6.27192C0.787598 7.22817 1.5751 8.01567 2.53135 8.01567H6.10322C7.05947 8.01567 7.84697 7.22817 7.84697 6.27192V2.72817C7.8751 1.7438 7.0876 0.956299 6.10322 0.956299ZM6.60947 6.30005C6.60947 6.5813 6.38447 6.8063 6.10322 6.8063H2.53135C2.2501 6.8063 2.0251 6.5813 2.0251 6.30005V2.72817C2.0251 2.44692 2.2501 2.22192 2.53135 2.22192H6.10322C6.38447 2.22192 6.60947 2.44692 6.60947 2.72817V6.30005Z"
                    fill=""
                  />
                  <path
                    d="M15.4689 0.956299H11.8971C10.9408 0.956299 10.1533 1.7438 10.1533 2.70005V6.27192C10.1533 7.22817 10.9408 8.01567 11.8971 8.01567H15.4689C16.4252 8.01567 17.2127 7.22817 17.2127 6.27192V2.72817C17.2127 1.7438 16.4252 0.956299 15.4689 0.956299ZM15.9752 6.30005C15.9752 6.5813 15.7502 6.8063 15.4689 6.8063H11.8971C11.6158 6.8063 11.3908 6.5813 11.3908 6.30005V2.72817C11.3908 2.44692 11.6158 2.22192 11.8971 2.22192H15.4689C15.7502 2.22192 15.9752 2.44692 15.9752 2.72817V6.30005Z"
                    fill=""
                  />
                  <path
                    d="M6.10322 9.92822H2.53135C1.5751 9.92822 0.787598 10.7157 0.787598 11.672V15.2438C0.787598 16.2001 1.5751 16.9876 2.53135 16.9876H6.10322C7.05947 16.9876 7.84697 16.2001 7.84697 15.2438V11.7001C7.8751 10.7157 7.0876 9.92822 6.10322 9.92822ZM6.60947 15.272C6.60947 15.5532 6.38447 15.7782 6.10322 15.7782H2.53135C2.2501 15.7782 2.0251 15.5532 2.0251 15.272V11.7001C2.0251 11.4188 2.2501 11.1938 2.53135 11.1938H6.10322C6.38447 11.1938 6.60947 11.4188 6.60947 11.7001V15.272Z"
                    fill=""
                  />
                  <path
                    d="M15.4689 9.92822H11.8971C10.9408 9.92822 10.1533 10.7157 10.1533 11.672V15.2438C10.1533 16.2001 10.9408 16.9876 11.8971 16.9876H15.4689C16.4252 16.9876 17.2127 16.2001 17.2127 15.2438V11.7001C17.2127 10.7157 16.4252 9.92822 15.4689 9.92822ZM15.9752 15.272C15.9752 15.5532 15.7502 15.7782 15.4689 15.7782H11.8971C11.6158 15.7782 11.3908 15.5532 11.3908 15.272V11.7001C11.3908 11.4188 11.6158 11.1938 11.8971 11.1938H15.4689C15.7502 11.1938 15.9752 11.4188 15.9752 11.7001V15.272Z"
                    fill=""
                  />
                </svg>`,
                label: "Transaksi",
                route: "/transaksi",
                roles: [1, 2, 3, 4],
                children: [
                    { label: "Tahapan APBD", route: "/transaksi/tahapan-dets" },
                    { label: "Data SIPD", route: "/transaksi/uploadrekapsipd" },
                    { label: "Marking Data", route: "/home" },
                ],
            },
            {
                icon: `<svg 
        xmlns="http://www.w3.org/2000/svg" 
        fill="none" 
        viewBox="0 0 24 24" 
        stroke-width="1.5" 
        stroke="currentColor" 
        class="size-6">
        <path stroke-linecap="round" 
        stroke-linejoin="round" 
        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25M9 16.5v.75m3-3v3M15 12v5.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
        </svg>`,
                label: "Laporan",
                route: "/laporan",
                roles: [1, 2, 3, 4, 5],
                children: [
                    { label: "Tahapan APBD", route: "/laporan/tahapan" },
                    { label: "Ringkasan APBD", route: "/laporan/rekapapbd" },
                    { label: "Belanja Terbesar", route: "/laporan/belanjamax" },
                    {
                        label: "Mandatory Spending",
                        route: "/laporan/rekapapbd",
                    },
                    {
                        label: "Standar Pelayanan Minimal",
                        route: "/laporan/rekapapbd",
                    },
                    { label: "Sumber Dana", route: "/laporan/rekapapbd" },
                    { label: "Dokumen", route: "/laporan/file" },
                ],
            },
            {
                icon: `<svg
                class="w-5 fill-current"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75"
                />
              </svg>`,
                label: "Konfigurasi",
                route: "/konfigurasi",
                roles: [1, 2],
                children: [{ label: "User", route: "/konfigurasi/users" }],
            },
        ],
    },
]);
</script>

<template>
    <aside
        class="absolute left-0 top-0 z-9999 flex h-screen w-72.5 flex-col overflow-y-hidden duration-300 ease-linear dark:bg-boxdark lg:static lg:translate-x-0"
        :class="{
            'translate-x-0': sidebarStore.isSidebarOpen,
            '-translate-x-full': !sidebarStore.isSidebarOpen,
        }"
        ref="target"
    >
        <!-- SIDEBAR HEADER -->
        <div
            class="flex items-center justify-between gap-2 px-6 py-5.5 lg:py-6.5"
        >
            <Link :href="route('home')">
                <div class="flex flex-row items-center gap-2.5">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.8"
                        stroke="currentColor"
                        class="text-primary size-8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"
                        />
                    </svg>

                    <span
                        class="text-xl font-bold tracking-wide text-black dark:text-white"
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
                    class="fill-current"
                    width="20"
                    height="18"
                    viewBox="0 0 20 18"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M19 8.175H2.98748L9.36248 1.6875C9.69998 1.35 9.69998 0.825 9.36248 0.4875C9.02498 0.15 8.49998 0.15 8.16248 0.4875L0.399976 8.3625C0.0624756 8.7 0.0624756 9.225 0.399976 9.5625L8.16248 17.4375C8.31248 17.5875 8.53748 17.7 8.76248 17.7C8.98748 17.7 9.17498 17.625 9.36248 17.475C9.69998 17.1375 9.69998 16.6125 9.36248 16.275L3.02498 9.8625H19C19.45 9.8625 19.825 9.4875 19.825 9.0375C19.825 8.55 19.45 8.175 19 8.175Z"
                        fill=""
                    />
                </svg>
            </button>
        </div>
        <!-- SIDEBAR HEADER -->

        <div
            class="flex flex-col justify-between h-full overflow-y-auto duration-300 ease-linear no-scrollbar"
        >
            <!-- Sidebar Menu -->
            <nav class="px-4 py-4 mt-5 lg:mt-9 lg:px-6">
                <template v-for="menuGroup in menuGroups" :key="menuGroup.name">
                    <div>
                        <h3
                            class="mb-4 ml-4 text-sm font-medium text-bodydark2"
                        >
                            {{ menuGroup.name }}
                        </h3>

                        <ul class="mb-6 flex flex-col gap-1.5">
                            <template
                                v-for="(menuItem, index) in menuGroup.menuItems"
                            >
                                <SidebarItem
                                    v-if="
                                        menuItem.roles.includes(
                                            userStore.user.roleid
                                        )
                                    "
                                    :item="menuItem"
                                    :key="index"
                                    :index="index"
                                />
                            </template>
                        </ul>
                    </div>
                </template>
            </nav>
            <!-- Sidebar Menu -->

            <div
                class="w-full px-2 py-2 mx-auto mb-10 text-center border rounded-sm max-w-60 border-strokedark bg-boxdark shadow-default"
            >
                <p class="text-xs text-slate-500">
                    &copy; Copyright {{ configStore.copyrightYear }}
                    {{ configStore.copyrightName }}.<br />All rights reserved
                </p>
            </div>
        </div>
    </aside>
</template>
