<script setup>
import { useUserStore } from "@/stores/user";
import BaseButton from "@/components/landing/BaseButton.vue";
import NavLink from "./NavLink.vue";
import DropdownUser from "@/components/Header/DropdownUser.vue";
import { onMounted, ref } from "vue";
import { useConfigStore } from "@/stores/config";
import DarkModeSwitcher from "@/components/Header/DarkModeSwitcher.vue";

import { Link } from "@inertiajs/inertia-vue3";

const props = defineProps({
    showNavLink: {
        type: Boolean,
        default: true,
    },
});

const configStore = useConfigStore();
const userStore = useUserStore();

onMounted(() => {
    userStore.fetchUser();
});
</script>

<template>
    <nav id="navbar" class="relative z-10 w-full text-neutral-800">
        <div
            class="flex flex-col max-w-screen-xl px-8 py-4 mx-auto lg:items-center lg:justify-between lg:flex-row"
        >
            <div
                class="flex flex-col items-center space-x-4 lg:flex-row xl:space-x-8"
            >
                <div
                    class="flex flex-row items-center justify-between w-full py-6"
                >
                    <Link :href="route('home')">
                        <div class="flex flex-row items-center gap-2.5">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.8"
                                stroke="currentColor"
                                class="text-primary size-8 dark:text-secondary"
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

                    <!-- Hamburger Toggle BTN -->
                    <button
                        class="z-99999 block rounded-sm border border-stroke bg-white p-1.5 shadow-sm dark:border-strokedark dark:bg-boxdark lg:hidden"
                        @click="open = !open"
                    >
                        <span class="relative block h-5.5 w-5.5 cursor-pointer">
                            <span
                                class="absolute right-0 w-full h-full du-block"
                            >
                                <span
                                    class="relative top-0 left-0 my-1 block h-0.5 w-0 rounded-sm bg-black delay-[0] duration-200 ease-in-out dark:bg-white"
                                    :class="{ '!w-full delay-300': !open }"
                                ></span>
                                <span
                                    class="relative top-0 left-0 my-1 block h-0.5 w-0 rounded-sm bg-black delay-150 duration-200 ease-in-out dark:bg-white"
                                    :class="{ '!w-full delay-400': !open }"
                                ></span>
                                <span
                                    class="relative top-0 left-0 my-1 block h-0.5 w-0 rounded-sm bg-black delay-200 duration-200 ease-in-out dark:bg-white"
                                    :class="{ '!w-full delay-500': !open }"
                                ></span>
                            </span>
                            <span
                                class="absolute right-0 w-full h-full rotate-45 du-block"
                            >
                                <span
                                    class="absolute left-2.5 top-0 block h-full w-0.5 rounded-sm bg-black delay-300 duration-200 ease-in-out dark:bg-white"
                                    :class="{ '!h-0 delay-[0]': !open }"
                                ></span>
                                <span
                                    class="delay-400 absolute left-0 top-2.5 block h-0.5 w-full rounded-sm bg-black duration-200 ease-in-out dark:bg-white"
                                    :class="{ '!h-0 dealy-200': !open }"
                                ></span>
                            </span>
                        </span>
                    </button>
                    <!-- Hamburger Toggle BTN -->
                </div>
            </div>

            <div class="flex items-center gap-8 2xsm:gap-7">
                <ul class="flex items-center gap-2 2xsm:gap-4">
                    <li>
                        <DarkModeSwitcher />
                    </li>
                </ul>

                <DropdownUser
                    v-if="userStore.user"
                    :user="userStore.user"
                    :always-show-detail="true"
                    :class="[open ? 'flex' : 'hidden lg:flex']"
                    class="space-x-3"
                />

                <div
                    v-else-if="
                        route().current() == 'home' ||
                        route().current() == 'create-store'
                    "
                    :class="[open ? 'flex' : 'hidden lg:flex']"
                    class="space-x-3"
                >
                    <Link :href="route('login')">
                        <BaseButton
                            class="px-6 xl:px-8 py-2 mt-2 bg-inherit !font-medium text-gradient border border-[#0c66ee]"
                        >
                            Masuk
                        </BaseButton>
                    </Link>
                    <Link :href="route('register')">
                        <BaseButton
                            class="px-6 xl:px-8 py-2 mt-2 !font-medium bg-gradient-to-r from-[#468ef9] to-[#0c66ee] text-white"
                        >
                            Daftar
                        </BaseButton>
                    </Link>
                </div>
            </div>
        </div>
    </nav>
</template>
<script>
export default {
    name: "BaseNavbar",
    data() {
        return {
            open: false,
            dropdownNavbar: false,
        };
    },
    methods: {
        dropdownToggler() {
            this.dropdownNavbar = !this.dropdownNavbar;
        },
    },
};
</script>
