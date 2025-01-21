<script setup>
import { ref, onUpdated, watch, onMounted, computed } from "vue";
import InputGroup from "@/components/Forms/InputGroup.vue";
import AlertWarning from "@/components/Alerts/AlertWarning.vue";
import CustomButton from "@/components/Forms/CustomButton.vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import SuccessDialog from "@/components/Dialogs/SuccessDialog.vue";
import { useStoreConfigStore } from "@/stores/store-config";

const props = defineProps({
    showCloseButton: {
        type: Boolean,
    },
    autoClearData: {
        type: Boolean,
        default: true,
    },
});
const emit = defineEmits(["close"]);

const storeConfigStore = useStoreConfigStore();

const initialForm = ref([]);

const form = ref([]);

const formValidation = ref([]);

function clearErrorMessage() {
    storeConfigStore.errorMessage = "";
}

async function getConfig() {
    await storeConfigStore.getConfig();

    const requiredFields = ["store_acronym"];

    form.value = [];

    let i = 0;
    for (const key in storeConfigStore.data) {
        form.value.push({
            id: i + key,
            key: key,
            value: storeConfigStore.data[key],
            required: requiredFields.includes(key),
        });

        i++;
    }

    if (form.value.filter((x) => x.key === "store_acronym").length === 0) {
        form.value.push({
            id: i + "store_acronym",
            key: "store_acronym",
            value: "",
            required: true,
        });
    }

    initialForm.value = JSON.parse(JSON.stringify(form.value));

    formValidation.value = form.value.map((e) => {
        return {
            id: e.id,
            key: "",
            value: "",
        };
    });
}

async function saveItem() {
    if (!validateItem()) return;

    await storeConfigStore.saveItem(form.value);

    await getConfig();
}

function validateItem() {
    let result = true;

    form.value.forEach((item, index) => {
        if (!item.key || !item.value) {
            result = false;

            formValidation.value[index].key = "Key diperlukan";
            formValidation.value[index].value = "Value diperlukan";
        }
    });

    return result;
}

function addConfig() {
    form.value.push({
        id: form.value.length,
        key: "",
        value: "",
    });

    formValidation.value.push({
        id: form.value.length,
        key: "",
        value: "",
    });

    reOrderConfig();
}

async function deleteConfig(item) {
    if (item.required) return;

    // Check if the item is in the initial form
    if (initialForm.value.find((x) => x.id === item.id)) {
        await storeConfigStore.deleteItem(item);
        await getConfig();
        return;
    }

    form.value.splice(form.value.indexOf(item), 1);
    formValidation.value.splice(formValidation.value.indexOf(item), 1);

    reOrderConfig();
}

function reOrderConfig() {
    form.value.forEach((item, index) => {
        item.id = index + item.key;
    });

    formValidation.value.forEach((item, index) => {
        item.id = index + item.key;
    });
}

const formChanged = computed(() => {
    return JSON.stringify(form.value) !== JSON.stringify(initialForm.value);
});

function close(value) {
    emit("close", value);
}

onMounted(() => {
    getConfig();
});
</script>

<template>
    <DefaultCard
        card-title="Konfigurasi Toko"
        :show-close-button="props.showCloseButton"
        @close="close(false)"
    >
        <div class="p-6.5">
            <AlertWarning
                v-if="storeConfigStore.errorMessage"
                @close="clearErrorMessage"
                :description="storeConfigStore.errorMessage"
                class="mb-6"
            />

            <table class="w-full table-fixed">
                <tr class="[&>th]:pb-1.5 [&>th]:text-sm">
                    <th class="text-start">Key</th>
                    <th class="w-4"></th>
                    <th class="text-start">Value</th>
                    <th class="w-4"></th>
                    <th class="w-8"></th>
                </tr>

                <template v-for="(item, index) in form" :key="item.id">
                    <tr class="[&>td]:pb-2 [&>td]:align-top">
                        <td>
                            <InputGroup
                                :id="item.id + '-key'"
                                v-model="item.key"
                                class="mb-0"
                                :warning="formValidation[index].key"
                            />
                        </td>
                        <td></td>
                        <td>
                            <InputGroup
                                :id="item.id + '-value'"
                                v-model="item.value"
                                class="mb-0"
                                :warning="formValidation[index].value"
                            />
                        </td>
                        <td></td>
                        <td>
                            <button
                                :id="`subtract-${item.id}`"
                                type="button"
                                class="p-2 my-1.5 text-gray-500 duration-300 ease-linear bg-gray-200 rounded-lg dark:bg-slate-700 dark:text-gray-300 hover:bg-gray-400 hover:bg-opacity-40 dark:hover:bg-slate-600"
                                :class="{
                                    '!text-gray-300 !bg-gray-100 cursor-default dark:!text-gray-400 dark:!bg-slate-800':
                                        item.required,
                                }"
                                @click="deleteConfig(item)"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="3"
                                    stroke="currentColor"
                                    class="size-3"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M5 12h14"
                                    />
                                </svg>
                            </button>
                        </td>
                    </tr>
                </template>
            </table>

            <!-- Add Config Button -->
            <div class="flex justify-end mt-2 gap-x-4">
                <CustomButton
                    @click="addConfig"
                    :loading="storeConfigStore.saveStatus === 'loading'"
                    color="bg-secondary"
                    :is-full="false"
                    padding="py-2 px-4"
                    class="text-sm"
                >
                    + Tambah
                </CustomButton>
            </div>

            <div class="flex flex-col-reverse gap-x-4 sm:flex-row">
                <CustomButton
                    @click="saveItem"
                    :loading="storeConfigStore.saveStatus === 'loading'"
                    :enable="formChanged"
                    color="bg-primary"
                    :is-full="true"
                    padding="p-3"
                    margin="mt-4"
                >
                    Simpan
                </CustomButton>
            </div>
        </div>
    </DefaultCard>
</template>
