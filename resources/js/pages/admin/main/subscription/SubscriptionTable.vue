<script setup>
import { ref, onMounted, computed, watch } from "vue";
import ItemActionButton from "@/components/ItemActionButton.vue";
import CustomDialog from "@/components/Dialogs/CustomDialog.vue";
import DeleteSubscriptionConfirmation from "./DeleteSubscriptionConfirmation.vue";
import CheckboxGroup from "@/components/Forms/CheckboxGroup.vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import { useUserStore } from "@/stores/user";
import { useSubscriptionStore } from "@/stores/subscription";
import CustomSwitch from "@/components/Forms/CustomSwitch.vue";
import StatusLabel from "@/components/StatusLabel.vue";
import SubscriptionForm from "./SubscriptionForm.vue";
import { Link } from "@inertiajs/inertia-vue3";

const props = defineProps({
    title: {
        type: String,
    },
});

const userStore = useUserStore();
const subscriptionStore = useSubscriptionStore();

const selectedItems = ref([]);
const isAllItemsSelected = ref(false);

const itemFormDialog = ref(null);
const deleteItemDialog = ref(null);

function showItemFormDialog(item) {
    if (item) {
        selectedItems.value = [item];
    }

    itemFormDialog.value.showModal();
}

function showDeleteItemDialog(item) {
    deleteItemDialog.value.showModal();
    if (item) {
        selectedItems.value = [item];
    }
}

function onItemFormDialogClosed(value) {
    itemFormDialog.value.close(value);

    if (value) {
        subscriptionStore.query.page = subscriptionStore.data.current_page;
        getData();
    }

    selectedItems.value = [];
}

function onDeleteItemDialogClosed(value) {
    deleteItemDialog.value.close(value);

    if (selectedItems.value.length == 1 && value == false) {
        selectedItems.value = [];
    }

    if (value) {
        selectedItems.value = [];
        subscriptionStore.query.page = subscriptionStore.data.current_page;
        getData();
    }
}

async function getData(params) {
    await subscriptionStore.getAllItems(params);
}

async function disableOrEnableItem(item) {
    await subscriptionStore.disableOrEnableItem(item);
}

const changePage = (page) => {
    subscriptionStore.query.page = page;

    getData();

    document
        .getElementById("pagetop")
        ?.scrollIntoView({ block: "start", behavior: "smooth" });
};

watch(
    () => selectedItems.value.length,
    (newValue, oldValue) => {
        console.log("current page: ", subscriptionStore.data.current_page);
    }
);

watch(
    () => isAllItemsSelected.value,
    (newValue, oldValue) => {
        if (!oldValue && newValue) {
            selectedItems.value = JSON.parse(
                JSON.stringify(subscriptionStore.items)
            );
        }

        if (
            oldValue &&
            !newValue &&
            selectedItems.value.length === subscriptionStore.items?.length
        ) {
            selectedItems.value = [];
        }
    }
);

const selectionMode = computed(() => selectedItems.value?.length > 0);

function canEdit(item) {
    if (selectionMode.value) {
        return false;
    }

    if (userStore.user.role_id <= 1) {
        return true;
    }

    return true;
}

onMounted(() => {
    getData();
    itemFormDialog.value = document.querySelector("#itemFormDialog");
    deleteItemDialog.value = document.querySelector("#deleteItemDialog");
});

defineExpose({
    getData,
    showItemFormDialog,
    showDeleteItemDialog,
    selectionMode,
    selectedItems,
});
</script>

<template>
    <DefaultCard
        :showShadow="false"
        :showBorder="false"
        class="px-4 pt-6 pb-2.5 sm:px-6.5 xl:pb-1"
    >
        <h4
            v-if="props.title"
            class="mb-6 text-xl font-semibold text-gray-900 dark:text-gray-200"
        >
            {{ props.title }}
        </h4>

        <!-- Table -->
        <div class="flex flex-col overflow-x-auto">
            <table class="table-fixed">
                <thead>
                    <tr
                        class="rounded-sm text-gray-500 bg-gray-100 dark:bg-gray-700 [&>th]:py-2.5 [&>th]:px-4 [&>th]:text-left duration-300 ease-linear"
                    >
                        <th class="w-0">
                            <CheckboxGroup :for="'formCheckbox'">
                                <input
                                    type="checkbox"
                                    :id="'formCheckbox'"
                                    class="sr-only taskCheckbox"
                                    :value="isAllItemsSelected"
                                    v-model="isAllItemsSelected"
                                />
                            </CheckboxGroup>
                        </th>

                        <th>
                            <h5
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Tgl. Dibuat
                            </h5>
                        </th>

                        <th>
                            <h5
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Kustomer
                            </h5>
                        </th>

                        <th>
                            <h5
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Paket
                            </h5>
                        </th>

                        <th>
                            <h5
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Total
                            </h5>
                        </th>

                        <th>
                            <h5
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Status
                            </h5>
                        </th>

                        <th>
                            <h5
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Durasi
                            </h5>
                        </th>

                        <th>
                            <h5
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Tgl. Mulai
                            </h5>
                        </th>

                        <th>
                            <h5
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Tgl. Berakhir
                            </h5>
                        </th>

                        <th class="w-0 max-sm:text-end">
                            <h5
                                v-if="!selectionMode"
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Aksi
                            </h5>
                        </th>

                        <th class="w-0 max-sm:text-end">
                            <h5
                                v-if="!selectionMode"
                                class="text-sm font-medium uppercase xsm:text-base dark:text-gray-400"
                            >
                                Tagihan
                            </h5>
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="(item, key) in subscriptionStore.items"
                        :key="key"
                        class="hover:bg-secondary hover:bg-opacity-10 dark:hover:bg-opacity-5 [&>td]:py-2.5 [&>td]:px-4 [&>td]:text-sm duration-300 ease-linear"
                        :class="{
                            'border-b border-stroke dark:border-strokedark':
                                key !== subscriptionStore.items.length - 1,
                            'bg-secondary bg-opacity-20 dark:bg-opacity-10':
                                selectedItems
                                    .map((i) => i.id)
                                    .includes(item.id),
                        }"
                    >
                        <!-- Checkbox -->
                        <td class="py-2.5 px-4 w-0">
                            <CheckboxGroup :for="'formCheckbox_' + item.id">
                                <input
                                    type="checkbox"
                                    :id="'formCheckbox_' + item.id"
                                    class="sr-only taskCheckbox"
                                    :value="item"
                                    v-model="selectedItems"
                                />
                            </CheckboxGroup>
                        </td>

                        <!-- Date Created -->
                        <td>
                            <p class="text-gray-900 dark:text-gray-200">
                                {{ $formatDate.formatDate(item.created_at) }}
                            </p>
                            <p
                                v-if="item.id"
                                class="mt-0.5 text-xs text-gray-400 dark:text-gray-400"
                            >
                                {{ item.id }}
                            </p>
                        </td>

                        <!-- Customer -->
                        <td>
                            <p
                                class="text-gray-900 dark:text-gray-200 line-clamp-2 overflow-ellipsis"
                            >
                                {{ item.customer?.name }}
                            </p>
                        </td>

                        <!-- Plan -->
                        <td>
                            <p class="text-gray-900 dark:text-gray-200">
                                {{ item.plan?.name }}
                            </p>
                        </td>

                        <!-- Total Price -->
                        <td>
                            <p class="text-gray-900 dark:text-gray-200">
                                Rp {{ $formatCurrency(item.amount) }}
                            </p>
                        </td>

                        <!-- Status -->
                        <td>
                            <StatusLabel
                                v-if="item.status == 'Active'"
                                :name="item.status"
                                status="success"
                            />

                            <StatusLabel
                                v-else-if="item.status == 'Pending Payment'"
                                :name="item.status"
                                status="warning"
                            />

                            <StatusLabel
                                v-else-if="item.status == 'Expired'"
                                :name="item.status"
                                status="danger"
                            />

                            <StatusLabel v-else :name="item.status" />
                        </td>

                        <!-- Duration -->
                        <td>
                            <p class="text-gray-900 dark:text-gray-200">
                                {{ item.duration_text }}
                            </p>
                        </td>

                        <!-- Start Date -->
                        <td>
                            <p class="text-gray-900 dark:text-gray-200">
                                {{
                                    $formatDate.formatDate(
                                        item.date_subscribed,
                                        {
                                            year: "numeric",
                                            month: "short",
                                            day: "numeric",
                                        }
                                    )
                                }}
                            </p>
                        </td>

                        <!-- End Date -->
                        <td>
                            <p class="text-gray-900 dark:text-gray-200">
                                {{
                                    $formatDate.formatDate(item.valid_to, {
                                        year: "numeric",
                                        month: "short",
                                        day: "numeric",
                                    })
                                }}
                            </p>
                        </td>

                        <!-- Actions -->
                        <td class="py-2.5 px-4">
                            <ItemActionButton
                                v-if="!selectionMode && canEdit(item)"
                                @update-item="showItemFormDialog(item)"
                                @delete-item="showDeleteItemDialog(item)"
                            />
                        </td>

                        <!-- Invoice -->
                        <td class="py-2.5 px-4">
                            <Link
                                :href="
                                    route('invoice.detail', {
                                        id: item.invoice_id,
                                    })
                                "
                                class="flex items-center justify-center gap-1 text-sm text-right text-primary dark:text-secondary hover:text-primary/90 dark:hover:text-secondary/90 text-nowrap"
                            >
                                <p>Lihat Tagihan</p>

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    class="w-4 h-4 pt-[1.5px]"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5l7 7-7 7"
                                    />
                                </svg>
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            class="flex flex-col items-start justify-between gap-2 py-6 sm:items-center sm:flex-row"
        >
            <vue-awesome-paginate
                v-if="subscriptionStore?.data?.current_page"
                :total-items="subscriptionStore.data.total"
                :items-per-page="subscriptionStore.data.per_page"
                :max-pages-shown="5"
                v-model="subscriptionStore.data.current_page"
                @click="changePage"
            />
            <p class="text-xs font-light text-gray-400">
                Showing {{ subscriptionStore.data?.from }} to
                {{ subscriptionStore.data?.to }} of
                {{ subscriptionStore.data?.total }} entries
            </p>
        </div>

        <CustomDialog
            id="itemFormDialog"
            :show-cancel="true"
            @cancel="onItemFormDialogClosed"
        >
            <SubscriptionForm
                :show-close-button="true"
                :item="selectedItems[0]"
                @close="onItemFormDialogClosed"
                class="max-sm:w-full sm:min-w-[400px] max-w-[400px]"
            />
        </CustomDialog>

        <CustomDialog id="deleteItemDialog" :show-cancel="true">
            <DeleteSubscriptionConfirmation
                :show-close-button="true"
                :items="selectedItems"
                @close="onDeleteItemDialogClosed"
                class="max-sm:w-full sm:min-w-[300px] max-w-[300px]"
            />
        </CustomDialog>
    </DefaultCard>
</template>
