<script setup>
import { onClickOutside } from "@vueuse/core";
import { ref } from "vue";
import { useUserStore } from "@/stores/user";
import { useDarkModeStore } from "@/stores/darkMode";
import { Link } from "@inertiajs/inertia-vue3";

const props = defineProps({
    user: Object,
    alwaysShowDetail: {
        type: Boolean,
        default: false,
    },
});

const target = ref(null);
const dropdownOpen = ref(false);

const userStore = useUserStore();
const darkModeStore = useDarkModeStore();

onClickOutside(target, () => {
    dropdownOpen.value = false;
});

function logout() {
    userStore.clearUser();
    darkModeStore.setDarkMode(false);
    localStorage.clear();
    window.location = route("home");
}
</script>

<template>
    <div class="relative" ref="target">
        <div
            class="flex items-center gap-4 cursor-pointer max-md:ml-2 max-lg:ml-5 max-lg:mt-2"
            @click.prevent="dropdownOpen = !dropdownOpen"
        >
            <div class="flex items-center gap-4">
                <span class="w-10 h-10 rounded-full">
                    <img src="@/assets/images/user/user-icon.png" alt="User" />
                </span>

                <span class="">
                    <span
                        class="block text-sm font-medium text-black dark:text-gray-300"
                        >{{ props.user.name }}</span
                    >
                    <span
                        class="block text-xs font-medium text-black dark:text-gray-300"
                    >
                        {{ props.user.role.name }}</span
                    >
                </span>
            </div>

            <svg
                :class="dropdownOpen && 'rotate-180'"
                class="fill-current dark:fill-gray-300"
                width="12"
                height="8"
                viewBox="0 0 12 8"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M0.410765 0.910734C0.736202 0.585297 1.26384 0.585297 1.58928 0.910734L6.00002 5.32148L10.4108 0.910734C10.7362 0.585297 11.2638 0.585297 11.5893 0.910734C11.9147 1.23617 11.9147 1.76381 11.5893 2.08924L6.58928 7.08924C6.26384 7.41468 5.7362 7.41468 5.41077 7.08924L0.410765 2.08924C0.0853277 1.76381 0.0853277 1.23617 0.410765 0.910734Z"
                    fill=""
                />
            </svg>
        </div>

        <!-- Dropdown Start -->
        <transition name="fade-down">
            <div
                v-show="dropdownOpen"
                class="absolute right-0 mt-4 flex w-62.5 flex-col rounded-lg border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark"
                :class="{
                    'max-lg:left-0 top-12': props.alwaysShowDetail,
                }"
            >
                <ul
                    class="flex flex-col gap-4 px-5 py-4 border-b border-stroke dark:border-strokedark"
                >
                    <li
                        v-if="
                            route().current() == 'home' ||
                            route().current() == 'login' ||
                            route().current() == 'signup'
                        "
                    >
                        <Link
                            :href="route('admin.dashboard')"
                            class="flex items-center gap-3.5 text-sm font-medium duration-100 ease-in-out hover:text-primary dark:hover:text-secondary dark:text-gray-200 [&>svg]:hover:text-primary [&>svg]:hover:fill-primary [&>svg]:dark:hover:text-secondary [&>svg]:dark:hover:fill-secondary"
                        >
                            <svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                class="w-5 duration-100 ease-in-out fill-gray-500 dark:fill-gray-300"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M12 2C17.523 2 22 6.477 22 12C22 17.523 17.523 22 12 22C6.477 22 2 17.523 2 12C2 6.477 6.477 2 12 2ZM16.596 7.404C16.4971 7.30478 16.3663 7.24366 16.2267 7.2314C16.0871 7.21915 15.9477 7.25654 15.833 7.337C12.943 9.365 11.313 10.567 10.939 10.939C10.658 11.2206 10.5001 11.6022 10.5001 12C10.5001 12.3978 10.658 12.7794 10.939 13.061C11.2206 13.342 11.6022 13.4999 12 13.4999C12.3978 13.4999 12.7794 13.342 13.061 13.061C13.28 12.841 14.479 11.21 16.659 8.164C16.7401 8.05035 16.7783 7.91161 16.7668 7.77245C16.7553 7.63329 16.6947 7.50274 16.596 7.404ZM17.5 11C17.2348 11 16.9804 11.1054 16.7929 11.2929C16.6054 11.4804 16.5 11.7348 16.5 12C16.5 12.2652 16.6054 12.5196 16.7929 12.7071C16.9804 12.8946 17.2348 13 17.5 13C17.7652 13 18.0196 12.8946 18.2071 12.7071C18.3946 12.5196 18.5 12.2652 18.5 12C18.5 11.7348 18.3946 11.4804 18.2071 11.2929C18.0196 11.1054 17.7652 11 17.5 11ZM6.5 11C6.23478 11 5.98043 11.1054 5.79289 11.2929C5.60536 11.4804 5.5 11.7348 5.5 12C5.5 12.2652 5.60536 12.5196 5.79289 12.7071C5.98043 12.8946 6.23478 13 6.5 13C6.76522 13 7.01957 12.8946 7.20711 12.7071C7.39464 12.5196 7.5 12.2652 7.5 12C7.5 11.7348 7.39464 11.4804 7.20711 11.2929C7.01957 11.1054 6.76522 11 6.5 11ZM8.818 7.404C8.72502 7.31116 8.61467 7.23753 8.49324 7.18734C8.37181 7.13714 8.24169 7.11135 8.11029 7.11144C7.9789 7.11154 7.84881 7.13751 7.72745 7.18788C7.60609 7.23825 7.49584 7.31202 7.403 7.405C7.21549 7.59277 7.11026 7.84734 7.11044 8.11271C7.11054 8.2441 7.13651 8.37419 7.18688 8.49555C7.23725 8.61691 7.31102 8.72716 7.404 8.82C7.59177 9.00751 7.84634 9.11274 8.11171 9.11256C8.37707 9.11237 8.63149 9.00677 8.819 8.819C9.00651 8.63123 9.11174 8.37666 9.11156 8.11129C9.11137 7.84593 9.00577 7.59151 8.818 7.404ZM12 5.5C11.7348 5.5 11.4804 5.60536 11.2929 5.79289C11.1054 5.98043 11 6.23478 11 6.5C11 6.76522 11.1054 7.01957 11.2929 7.20711C11.4804 7.39464 11.7348 7.5 12 7.5C12.2652 7.5 12.5196 7.39464 12.7071 7.20711C12.8946 7.01957 13 6.76522 13 6.5C13 6.23478 12.8946 5.98043 12.7071 5.79289C12.5196 5.60536 12.2652 5.5 12 5.5Z"
                                />
                            </svg>

                            Dashboard
                        </Link>
                    </li>
                    <li>
                        <Link
                            :href="route('admin.profile')"
                            class="flex items-center gap-3.5 text-sm font-medium duration-100 ease-in-out hover:text-primary dark:hover:text-secondary dark:text-gray-200 [&>svg]:hover:text-primary [&>svg]:hover:fill-primary [&>svg]:dark:hover:text-secondary [&>svg]:dark:hover:fill-secondary"
                        >
                            <svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                class="w-5 duration-100 ease-in-out fill-gray-500 dark:fill-gray-300"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    fill-rule="evenodd"
                                    clip-rule="evenodd"
                                    d="M12 2C13.3132 1.99999 14.6136 2.25865 15.8268 2.76119C17.0401 3.26373 18.1425 4.00033 19.0711 4.92891C19.9997 5.8575 20.7363 6.95989 21.2388 8.17315C21.7414 9.3864 22 10.6868 22 12C22 17.5228 17.5229 22 12 22C6.47719 22 2.00002 17.5228 2.00002 12C2.00002 6.47717 6.47719 2 12 2ZM13 13H11C8.52431 13 6.39886 14.4994 5.48212 16.6398C6.93262 18.6737 9.31144 20 12 20C14.6886 20 17.0674 18.6737 18.5179 16.6396C17.6012 14.4994 15.4757 13 13 13ZM12 5C10.3432 5 9 6.34316 9 8C9 9.65684 10.3432 11 12 11C13.6568 11 15 9.65684 15 8C15 6.34316 13.6569 5 12 5Z"
                                />
                            </svg>
                            My Profile
                        </Link>
                    </li>
                </ul>

                <button
                    @click="logout"
                    class="flex items-center gap-3.5 py-4 px-5 text-sm font-medium duration-100 ease-in-out hover:text-danger dark:hover:text-danger dark:text-gray-200 [&>svg]:hover:text-danger [&>svg]:hover:fill-danger"
                >
                    <svg
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        class="w-5 duration-100 ease-in-out fill-gray-500 dark:fill-gray-300"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M22.6955 12.7078L15.3618 19.7066C14.707 20.3315 13.5721 19.8941 13.5721 18.9984V14.9991H7.63525C7.05467 14.9991 6.58758 14.5533 6.58758 13.9992V9.99989C6.58758 9.44581 7.05467 9.00006 7.63525 9.00006H13.5721V5.00072C13.5721 4.1092 14.7027 3.66761 15.3618 4.2925L22.6955 11.2913C23.1015 11.6829 23.1015 12.3162 22.6955 12.7078ZM9.38137 19.4983V17.8319C9.38137 17.557 9.14564 17.332 8.85753 17.332H5.19068C4.41803 17.332 3.79379 16.7363 3.79379 15.9989V8.00022C3.79379 7.26284 4.41803 6.66711 5.19068 6.66711H8.85753C9.14564 6.66711 9.38137 6.44215 9.38137 6.16719V4.5008C9.38137 4.22585 9.14564 4.00089 8.85753 4.00089H5.19068C2.87708 4.00089 1 5.79226 1 8.00022V15.9989C1 18.2069 2.87708 19.9982 5.19068 19.9982H8.85753C9.14564 19.9982 9.38137 19.7733 9.38137 19.4983Z"
                        />
                    </svg>
                    Log Out
                </button>
            </div>
        </transition>
        <!-- Dropdown End -->
    </div>
</template>
