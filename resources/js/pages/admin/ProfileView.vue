<script setup>
import AdminDefaultLayout from "@/layouts/AdminDefaultLayout.vue";

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
        <AdminDefaultLayout>
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
                                        class="h-24 rounded-full aspect-square bg-slate-200 dark:bg-slate-700"
                                    ></div>

                                    <img
                                        v-else-if="userStore.user.avatar"
                                        :src="userStore.user.avatar"
                                        alt="User"
                                        class="opacity-50 size-24"
                                    />

                                    <img
                                        v-else
                                        src="@/assets/images/user/user-icon.png"
                                        alt="User"
                                        class="opacity-50 size-24"
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
                                            {{
                                                userStore.user.role?.name || "-"
                                            }}
                                        </DetailRow>

                                        <DetailRow
                                            label="Aktif"
                                            :auto-wrap="false"
                                        >
                                            <CustomSwitch
                                                v-model="userStore.isEnabled"
                                                :disable="true"
                                                :disable-margin="true"
                                                :small="true"
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
                </div>
            </div>
        </AdminDefaultLayout>
    </main>

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
</template>
