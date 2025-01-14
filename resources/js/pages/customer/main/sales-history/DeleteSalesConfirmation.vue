<script setup>
import { computed } from "vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import CustomButton from "@/components/Forms/CustomButton.vue";
import { useSalesStore } from "@/stores/sales";

const props = defineProps({
    showCloseButton: Boolean,
    items: {
        type: Array,
        default: [],
    },
});
const emit = defineEmits(["close"]);

const salesStore = useSalesStore();

async function deleteItems() {
    const result = await salesStore.deleteItems(props.items);

    if (result) {
        emit("close", true);
    }
}

const filteredItems = computed(() => {
    if (props.items.length > 6) {
        let displayItems = props.items.slice(0, 6);
        displayItems.push({ nama: `... ${props.items.length - 6} more` });
        return displayItems;
    }
    return props.items;
});

function close(value) {
    salesStore.deleteStatus = "";
    emit("close", value);
}
</script>

<template>
    <DefaultCard
        :show-close-button="props.showCloseButton"
        @close="close(false)"
    >
        <div class="px-6.5 py-8 flex flex-col items-center">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="mb-4 text-red-400 size-20"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"
                />
            </svg>

            <h2
                class="mb-4 text-center text-gray-700 text-title-lg dark:text-gray-200"
            >
                Apakah Anda yakin?
            </h2>
            <h2
                v-if="items.length == 1"
                class="mb-4 text-sm text-center text-gray-700 dark:text-gray-200"
            >
                Item <span class="font-bold">{{ items[0].code }}</span> akan
                dihapus
            </h2>
            <h2
                v-else
                class="mb-4 text-sm text-center text-gray-700 dark:text-gray-200"
            >
                Item berikut akan dihapus
            </h2>

            <div
                v-if="props.items && filteredItems.length > 1"
                class="flex flex-wrap justify-center gap-2 mb-2"
            >
                <div
                    v-for="item in filteredItems"
                    class="px-4 py-2 text-sm text-gray-500 rounded-full bg-slate-100 dark:text-bodydark1 dark:bg-gray-700"
                >
                    {{ item.code }}
                </div>
            </div>

            <div
                class="flex flex-col-reverse w-full mt-4 gap-x-2 gap-y-2 sm:flex-row"
            >
                <CustomButton
                    @click="close(false)"
                    :enable="salesStore.deleteStatus !== 'loading'"
                    color="bg-slate-400"
                    :is-full="true"
                    padding="py-2.5 px-6"
                >
                    Batal
                </CustomButton>
                <CustomButton
                    @click="deleteItems"
                    :loading="salesStore.deleteStatus === 'loading'"
                    :loading-text="`${salesStore.deleteProgress}/${props.items.length}`"
                    color="bg-danger"
                    :is-full="true"
                    padding="py-2.5 px-6"
                >
                    Hapus
                </CustomButton>
            </div>
        </div>
    </DefaultCard>
</template>
