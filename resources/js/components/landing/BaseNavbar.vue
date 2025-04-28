<script setup>
import { useUserStore } from "@/stores/user";
import BaseButton from "@/components/landing/BaseButton.vue";
import NavLink from "./NavLink.vue";
import DropdownUser from "@/components/Header/DropdownUser.vue";
import AdminDropdownUser from "@/components/Header/AdminDropdownUser.vue";
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

const scrolled = ref(false);
const scrollThreshold = 50;

const handleScroll = () => {
    scrolled.value = window.scrollY > scrollThreshold;
};

onMounted(() => {
    window.addEventListener("scroll", handleScroll);
    userStore.fetchUser();
});
</script>

<template>
    <nav
        id="navbar"
        class="sticky top-0 z-10 w-full transition-all duration-300 ease-linear bg-blue-50 dark:bg-gray-900"
        :class="{
            '!bg-transparent py-4': !scrolled,
            'bg-white dark:!bg-boxdark shadow-md py-0': scrolled,
        }"
    >
        <div
            class="flex flex-col max-w-screen-xl px-8 mx-auto lg:items-center lg:justify-between lg:flex-row"
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
                                class="text-xl font-bold tracking-wide text-gray-900 dark:text-gray-200"
                            >
                                {{ configStore.title }}
                            </span>
                        </div>
                    </Link>

                    <div class="flex gap-4">
                        <ul
                            v-if="!open"
                            class="flex items-center gap-2 2xsm:gap-4 lg:hidden"
                        >
                            <li>
                                <DarkModeSwitcher />
                            </li>
                        </ul>

                        <!-- Hamburger Toggle BTN -->
                        <button
                            class="z-99999 block rounded-sm border border-stroke bg-white p-1.5 shadow-sm dark:border-strokedark dark:bg-boxdark lg:hidden"
                            @click="open = !open"
                        >
                            <span
                                class="relative block h-5.5 w-5.5 cursor-pointer"
                            >
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
                    </div>
                    <!-- Hamburger Toggle BTN -->
                </div>
            </div>

            <div
                class="flex items-center gap-8 2xsm:gap-7"
                :class="{
                    'justify-between py-4': open,
                }"
            >
                <ul
                    class="flex items-center gap-2 2xsm:gap-4"
                    :class="{
                        'hidden lg:flex': !open,
                    }"
                >
                    <li>
                        <DarkModeSwitcher />
                    </li>
                </ul>

                <DropdownUser
                    v-if="userStore.user && userStore.user.role_id >= 3"
                    :user="userStore.user"
                    :always-show-detail="true"
                    :class="[open ? 'flex' : 'hidden lg:flex']"
                    class="space-x-3"
                />

                <AdminDropdownUser
                    v-else-if="userStore.user && userStore.user.role_id <= 2"
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
                            class="px-6 xl:px-8 py-2 mt-2 !font-medium bg-gradient-to-r from-[#468ef9] to-[#0c66ee] text-gray-200"
                        >
                            Daftar
                        </BaseButton>
                    </Link>
                    <a href="https://wa.me/6285727320485" target="_blank">
                        <BaseButton
                            class="flex items-center justify-center max-w-full gap-2 px-2 py-2 mt-2 text-green-600 border border-green-600 sm:px-4 bg-inherit hover:shadow-green-600/50"
                        >
                            <svg
                                fill="#000000"
                                version="1.1"
                                id="Capa_1"
                                xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                viewBox="0 0 30.667 30.667"
                                xml:space="preserve"
                                class="size-4 fill-green-600"
                            >
                                <g>
                                    <path
                                        d="M30.667,14.939c0,8.25-6.74,14.938-15.056,14.938c-2.639,0-5.118-0.675-7.276-1.857L0,30.667l2.717-8.017
                                                c-1.37-2.25-2.159-4.892-2.159-7.712C0.559,6.688,7.297,0,15.613,0C23.928,0.002,30.667,6.689,30.667,14.939z M15.61,2.382
                                                c-6.979,0-12.656,5.634-12.656,12.56c0,2.748,0.896,5.292,2.411,7.362l-1.58,4.663l4.862-1.545c2,1.312,4.393,2.076,6.963,2.076
                                                c6.979,0,12.658-5.633,12.658-12.559C28.27,8.016,22.59,2.382,15.61,2.382z M23.214,18.38c-0.094-0.151-0.34-0.243-0.708-0.427
                                                c-0.367-0.184-2.184-1.069-2.521-1.189c-0.34-0.123-0.586-0.185-0.832,0.182c-0.243,0.367-0.951,1.191-1.168,1.437
                                                c-0.215,0.245-0.43,0.276-0.799,0.095c-0.369-0.186-1.559-0.57-2.969-1.817c-1.097-0.972-1.838-2.169-2.052-2.536
                                                c-0.217-0.366-0.022-0.564,0.161-0.746c0.165-0.165,0.369-0.428,0.554-0.643c0.185-0.213,0.246-0.364,0.369-0.609
                                                c0.121-0.245,0.06-0.458-0.031-0.643c-0.092-0.184-0.829-1.984-1.138-2.717c-0.307-0.732-0.614-0.611-0.83-0.611
                                                c-0.215,0-0.461-0.03-0.707-0.03S9.897,8.215,9.56,8.582s-1.291,1.252-1.291,3.054c0,1.804,1.321,3.543,1.506,3.787
                                                c0.186,0.243,2.554,4.062,6.305,5.528c3.753,1.465,3.753,0.976,4.429,0.914c0.678-0.062,2.184-0.885,2.49-1.739
                                                C23.307,19.268,23.307,18.533,23.214,18.38z"
                                    />
                                </g>
                            </svg>
                            <span class="hidden sm:block"> Hubungi Kami </span>
                        </BaseButton>
                    </a>
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
