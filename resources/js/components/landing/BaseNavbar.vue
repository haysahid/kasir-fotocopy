<script setup>
import { useUserStore } from "@/stores/user";
import BaseButton from "@/components/landing/BaseButton.vue";
import NavLink from "./NavLink.vue";
import DropdownUser from "@/components/Header/DropdownUser.vue";
import { onMounted, ref } from "vue";

import { Link } from "@inertiajs/inertia-vue3";

const props = defineProps({
    showNavLink: {
        type: Boolean,
        default: true,
    },
});

const userStore = useUserStore();
const appTitle = ref(import.meta.env.VITE_APP_TITLE);

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
                    <Link to="/">
                        <div class="flex flex-row items-center gap-4">
                            <img
                                src="@/assets/images/logo/logo.png"
                                alt="Logo"
                            />
                            <span
                                class="text-3xl font-semibold tracking-wide text-black dark:text-white"
                                >{{ appTitle }}</span
                            >
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

            <DropdownUser
                v-if="userStore.user"
                :user="userStore.user"
                :always-show-detail="true"
                :class="[open ? 'flex' : 'hidden lg:flex']"
                class="space-x-3"
            />

            <div
                v-else
                :class="[open ? 'flex' : 'hidden lg:flex']"
                class="space-x-3"
            >
                <BaseButton
                    v-if="$route.name !== 'login'"
                    @click="$router('login')"
                    class="px-8 xl:px-10 py-3 mt-2 !font-medium bg-gradient-to-r from-[#468ef9] to-[#0c66ee] text-white"
                >
                    Masuk
                </BaseButton>
                <BaseButton
                    v-else
                    v-if="$route.name !== 'login'"
                    @click="$router('login')"
                    class="px-8 xl:px-10 py-3 mt-2 bg-inherit !font-medium text-gradient border border-[#0c66ee]"
                >
                    Masuk
                </BaseButton>
                <BaseButton
                    v-if="$route.name !== 'signup'"
                    @click="$router('signup')"
                    class="px-8 xl:px-10 py-3 mt-2 !font-medium bg-gradient-to-r from-[#468ef9] to-[#0c66ee] text-white"
                >
                    Daftar
                </BaseButton>
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
