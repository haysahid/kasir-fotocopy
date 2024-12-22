<script setup>
import { ref, onUpdated, computed, watch } from "vue";
import InputGroup from "@/components/Forms/InputGroup.vue";
import AlertWarning from "@/components/Alerts/AlertWarning.vue";
import CustomButton from "@/components/Forms/CustomButton.vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import CustomSwitch from "@/components/Forms/CustomSwitch.vue";
import { useSubscriptionStore } from "@/stores/subscription";
import CustomDatePicker from "@/components/Forms/CustomDatePicker.vue";

const props = defineProps({
    showCloseButton: {
        type: Boolean,
    },
    item: {
        type: Object,
    },
    autoClearData: {
        type: Boolean,
        default: true,
    },
});
const emit = defineEmits(["close"]);

const subscriptionStore = useSubscriptionStore();

const form = ref({
    user_id: null,
    date_subscribed: null,
    valid_to: null,
    date_unsubscribed: null,
});

const formValidation = ref({
    user_id: "",
    date_subscribed: "",
    valid_to: "",
    date_unsubscribed: "",
});

function clearErrorMessage() {
    subscriptionStore.errorMessage = "";
}

async function saveItem() {
    if (props.item) {
        updateItem();
    } else {
        addItem();
    }
}

async function addItem() {
    if (!validateAddItem()) return;

    const response = await subscriptionStore.addItem(form.value);

    if (response.meta.code === 201) {
        close(true);
    }
}

async function updateItem() {
    if (!validateUpdateItem()) return;

    const response = await subscriptionStore.updateItem(
        props.item.id,
        form.value
    );

    if (response && response.meta.code === 200) {
        close(true);
    } else {
        document
            .getElementById("pagetop")
            ?.scrollIntoView({ block: "start", behavior: "smooth" });
    }
}

function validateAddItem() {
    let result = true;

    if (!form.value.date_subscribed) {
        formValidation.value.date_subscribed =
            "Tanggal berlangganan harus diisi";
        result = false;
    }

    if (!form.value.valid_to) {
        formValidation.value.valid_to = "Berlaku sampai harus diisi";
        result = false;
    }

    return result;
}

function validateUpdateItem() {
    return validateAddItem();
}

watch(
    () => form.value.date_subscribed,
    (newValue) => {
        if (newValue && formValidation.value.date_subscribed) {
            formValidation.value.date_subscribed = "";
        }
    }
);

watch(
    () => form.value.valid_to,
    (newValue) => {
        if (newValue && formValidation.value.valid_to) {
            formValidation.value.valid_to = "";
        }
    }
);

watch(
    () => form.value.date_unsubscribed,
    (newValue) => {
        if (newValue && formValidation.value.date_unsubscribed) {
            formValidation.value.date_unsubscribed = "";
        }
    }
);

onUpdated(() => {
    if (props.item) {
        form.value = {
            user_id: props.item.user_id,
            date_subscribed: props.item.date_subscribed,
            valid_to: props.item.valid_to,
            date_unsubscribed: props.item.date_unsubscribed,
        };
    }
});

function close(value) {
    if (props.autoClearData) {
        form.value = {
            user_id: null,
            date_subscribed: null,
            valid_to: null,
            date_unsubscribed: null,
        };
    } else {
        form.value = {
            user_id: props.item.user_id,
            date_subscribed: props.item.date_subscribed,
            valid_to: props.item.valid_to,
            date_unsubscribed: props.item.date_unsubscribed,
        };
    }

    emit("close", value);
    subscriptionStore.saveStatus = "";
    subscriptionStore.errorMessage = "";
}
</script>

<template>
    <DefaultCard
        :card-title="props.item ? 'Ubah Langganan' : 'Tambah Langganan'"
        :show-close-button="props.showCloseButton"
        @close="close(false)"
    >
        <div class="p-6.5">
            <AlertWarning
                v-if="subscriptionStore.errorMessage"
                @close="clearErrorMessage"
                :description="subscriptionStore.errorMessage"
                class="mb-6"
            />

            <CustomDatePicker
                v-model="form.date_subscribed"
                label="Tanggal Berlangganan"
                placeholder="yyyy-mm-dd"
                class="mb-6"
            />

            <CustomDatePicker
                v-model="form.valid_to"
                label="Berlaku Sampai"
                placeholder="yyyy-mm-dd"
                class="mb-6"
            />

            <CustomDatePicker
                v-model="form.date_unsubscribed"
                label="Tanggal Berhenti Berlangganan"
                placeholder="yyyy-mm-dd"
                class="mb-6"
            />

            <div class="flex flex-col-reverse mt-6 gap-x-4 sm:flex-row">
                <CustomButton
                    @click="close(false)"
                    :enable="subscriptionStore.saveStatus !== 'loading'"
                    color="bg-danger"
                    :is-full="true"
                    padding="p-3"
                    margin="mt-4"
                >
                    Batal
                </CustomButton>

                <CustomButton
                    @click="saveItem"
                    :loading="subscriptionStore.saveStatus === 'loading'"
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
