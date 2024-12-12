<script setup>
import DefaultLayout from "@/layouts/DefaultLayout.vue";

import { ref, onMounted, inject } from "vue";
import CustomDialog from "@/components/Dialogs/CustomDialog.vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import CustomButton from "@/components/Forms/CustomButton.vue";
import BreadcrumbDefault from "@/components/BreadcrumbDefault.vue";
import StatusLabel from "@/components/StatusLabel.vue";
import DetailRow from "@/components/DetailRow.vue";
import CustomSwitch from "@/components/Forms/CustomSwitch.vue";
import ProfileForm from "@/components/Forms/ProfileForm.vue";
import { useUserStore } from "@/stores/user";
import { Link } from "@inertiajs/inertia-vue3";
import StoreNotFoundAlert from "./customer/main/StoreNotFoundAlert.vue";

const userStore = useUserStore();

const getProfileState = ref("");

// Dialog
const updateProfileDialog = ref(null);

async function getProfile() {
    getProfileState.value = "loading";

    try {
        if (await userStore.fetchUser()) {
            getProfileState.value = "success";
        } else {
            throw "Get profile failed!";
        }
    } catch (error) {
        console.error(error);
        getProfileState.value = "error";
    }
}

function showUpdateProfileDialog() {
    updateProfileDialog.value.showModal();
}

function onUpdateUserDialogClosed(value) {
    updateProfileDialog.value.close(value);

    if (value) {
        getProfile();
    }
}

onMounted(() => {
    getProfile();
    updateProfileDialog.value = document.querySelector("#updateProfileDialog");
});
</script>

<template>
    <main>
        <DefaultLayout>
            <div data-aos="fade-up" data-aos-once="true" class="mx-auto">
                <BreadcrumbDefault pageTitle="Profil Saya" />

                <div
                    v-if="userStore.user"
                    class="flex flex-col gap-9 md:flex-row"
                >
                    <div class="flex flex-col md:w-1/2 gap-9">
                        <DefaultCard
                            :showShadow="false"
                            :showBorder="false"
                            cardTitle="Pengguna"
                        >
                            <div class="p-6.5">
                                <div
                                    class="flex flex-col items-center justify-center gap-8 max-sm:flex-col"
                                >
                                    <div
                                        v-if="getProfileState === 'loading'"
                                        class="rounded-full h-35 aspect-square bg-slate-200 dark:bg-slate-700"
                                    ></div>

                                    <img
                                        v-else-if="userStore.user.avatar"
                                        :src="userStore.user.avatar"
                                        alt="User"
                                        class="opacity-50 size-35"
                                    />

                                    <img
                                        v-else
                                        src="@/assets/images/user/user-icon.png"
                                        alt="User"
                                        class="opacity-50 size-35"
                                    />

                                    <div
                                        v-if="
                                            userStore.user.role?.name ==
                                            'Super Admin'
                                        "
                                    >
                                        You are Super Admin
                                    </div>

                                    <div v-else class="flex flex-col w-full">
                                        <DetailRow label="Nama">
                                            {{ userStore.user.name || "-" }}
                                        </DetailRow>

                                        <DetailRow label="Email">
                                            {{ userStore.user.email || "-" }}
                                        </DetailRow>

                                        <DetailRow label="No. HP">
                                            {{ userStore.user.phone || "-" }}
                                        </DetailRow>

                                        <DetailRow
                                            label="Role"
                                            :auto-wrap="false"
                                        >
                                            <StatusLabel
                                                :name="
                                                    userStore.user.role?.name
                                                "
                                                :auto-scale="false"
                                            />
                                        </DetailRow>

                                        <DetailRow
                                            label="Aktif"
                                            :auto-wrap="false"
                                        >
                                            <CustomSwitch
                                                v-model="userStore.isEnabled"
                                                :disable="true"
                                                :disable-margin="true"
                                            />
                                        </DetailRow>

                                        <DetailRow label="Dibuat pada">
                                            {{
                                                $formatDate.formatDate(
                                                    userStore.user.created_at
                                                )
                                            }}
                                        </DetailRow>

                                        <DetailRow
                                            label="Diperbarui pada"
                                            :show-border-bottom="false"
                                        >
                                            {{
                                                $formatDate.formatDate(
                                                    userStore.user.updated_at
                                                )
                                            }}
                                        </DetailRow>

                                        <div
                                            v-if="getProfileState === 'loading'"
                                            class="flex flex-col-reverse mt-8 gap-x-4 gap-y-4 sm:flex-row"
                                        >
                                            <div
                                                class="w-full h-12 rounded-lg sm:max-w-24 bg-slate-200 dark:bg-slate-700"
                                            ></div>
                                        </div>

                                        <div
                                            v-else
                                            class="flex flex-col-reverse mt-8 gap-x-4 gap-y-4 sm:flex-row"
                                        >
                                            <CustomButton
                                                @click="showUpdateProfileDialog"
                                                color="bg-primary"
                                                :is-full="false"
                                                padding="py-3 px-8"
                                                margin="mt-4"
                                            >
                                                Ubah
                                            </CustomButton>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </DefaultCard>
                    </div>

                    <div class="flex flex-col gap-9 md:w-1/2">
                        <DefaultCard
                            :showShadow="false"
                            :showBorder="false"
                            cardTitle="Toko"
                        >
                            <div class="p-6.5">
                                <div
                                    v-if="
                                        userStore.user &&
                                        userStore.user.store.length > 0
                                    "
                                    class="flex flex-col items-center justify-center gap-8 max-sm:flex-col"
                                >
                                    <img
                                        v-if="userStore.user.store[0]?.logo"
                                        :src="userStore.user.store[0]?.logo"
                                        alt="Brand"
                                        class="opacity-50 size-35"
                                    />

                                    <svg
                                        v-else
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="text-gray-400 duration-300 ease-linear dark:text-gray-500 size-[140px]"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z"
                                        />
                                    </svg>

                                    <div class="flex flex-col w-full">
                                        <DetailRow label="Nama">
                                            {{
                                                userStore.user.store[0]?.name ||
                                                "-"
                                            }}
                                        </DetailRow>

                                        <DetailRow label="Deskripsi">
                                            {{
                                                userStore.user.store[0]
                                                    ?.description || "-"
                                            }}
                                        </DetailRow>

                                        <DetailRow label="Alamat">
                                            {{
                                                userStore.user.store[0]
                                                    ?.address || "-"
                                            }}
                                        </DetailRow>

                                        <DetailRow
                                            label="Dibuat pada"
                                            :showBorderBottom="false"
                                        >
                                            {{
                                                $formatDate.formatDate(
                                                    userStore.user.store[0]
                                                        ?.created_at
                                                )
                                            }}
                                        </DetailRow>
                                    </div>
                                </div>

                                <StoreNotFoundAlert v-else />
                            </div>
                        </DefaultCard>
                    </div>
                </div>
            </div>

            <CustomDialog
                v-if="userStore.user"
                id="updateProfileDialog"
                :show-cancel="true"
                @cancel="onUpdateUserDialogClosed"
            >
                <ProfileForm
                    :show-close-button="true"
                    :user="userStore.user"
                    :auto-clear-data="false"
                    class="sm:min-w-[400px] max-w-[400px]"
                    @close="onUpdateUserDialogClosed"
                />
            </CustomDialog>
        </DefaultLayout>
    </main>
</template>
