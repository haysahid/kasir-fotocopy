<script setup>
import { ref } from "vue";

const dialog = ref();

const props = defineProps({
    id: {
        type: String,
    },
    confirmText: {
        type: String,
        default: "Confirm",
    },
    cancelText: {
        type: String,
        default: "Cancel",
    },
    hideConfirm: {
        type: Boolean,
        default: false,
    },
    showCancel: {
        type: Boolean,
        default: false,
    },
    classes: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["confirm", "cancel"]);

const cancel = () => {
    dialog.value?.close();
    emit("cancel");
};

const confirm = () => {
    dialog.value?.close();
    emit("confirm");
};

const visible = ref(false);

const showModal = () => {
    dialog.value?.showModal();
    visible.value = true;
};

defineExpose({
    show: showModal,
    close: (returnVal) => dialog.value?.close(returnVal),
    visible,
});
</script>

<template>
    <dialog
        ref="dialog"
        :id="props.id"
        class="bg-transparent"
        @close="cancel"
        @keydown.esc="cancel"
    >
        <slot></slot>
    </dialog>
</template>
