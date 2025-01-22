<script setup>
import InputGroup from "@/components/Forms/InputGroup.vue";
import ItemActionButton from "@/components/ItemActionButton.vue";
import ProductItem from "../product/ProductItem.vue";
import { defineProps, defineEmits, computed } from "vue";

const props = defineProps({
    cartItem: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["updateItem", "removeItem"]);

function updateItem(item) {
    emit("updateItem", item);
}

function removeItem(item) {
    emit("removeItem", item);
}

const canSubtract = computed(() => props.cartItem.quantity > 1);
const canAdd = computed(() => true);
</script>

<template>
    <div
        class="flex flex-col gap-2.5 px-6 py-2.5 border-b border-stroke dark:border-strokedark duration-300 ease-linear"
    >
        <div class="flex flex-row gap-2">
            <ProductItem
                :product="props.cartItem.product"
                priceToShow="purchase_price"
                :isInCart="true"
                :isSelected="true"
                class="w-full"
            />

            <ItemActionButton
                @click="removeItem(props.cartItem.product)"
                :showEditButton="false"
                :showDeleteButton="true"
            />
        </div>

        <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <button
                    :id="`subtract-${props.cartItem.id}`"
                    type="button"
                    class="p-2 text-gray-500 duration-300 ease-linear bg-gray-200 rounded-lg dark:bg-slate-700 dark:text-gray-300 hover:bg-gray-400 hover:bg-opacity-40 dark:hover:bg-slate-600"
                    :class="{
                        '!text-gray-300 !bg-gray-100 cursor-default dark:!text-gray-400 dark:!bg-slate-800':
                            !canSubtract,
                    }"
                    @click="
                        () => {
                            if (canSubtract) {
                                props.cartItem.quantity--;
                            }
                        }
                    "
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

                <InputGroup
                    :id="`quantity-${props.cartItem.id}`"
                    type="number"
                    v-model="props.cartItem.quantity"
                    @change="
                        (e) => {
                            if (!canSubtract) {
                                props.cartItem.quantity = 1;
                            }

                            if (!canAdd) {
                                props.cartItem.quantity =
                                    props.cartItem.product.stock;
                            }

                            updateItem(props.cartItem);
                        }
                    "
                    class="!w-[80px] !mb-0"
                />

                <button
                    :id="`add-${props.cartItem.id}`"
                    type="button"
                    class="p-2 text-gray-500 duration-300 ease-linear bg-gray-200 rounded-lg dark:bg-slate-700 dark:text-gray-300 hover:bg-gray-400 hover:bg-opacity-40 dark:hover:bg-slate-600"
                    :class="{
                        '!text-gray-300 !bg-gray-100 cursor-default dark:!text-gray-400 dark:!bg-slate-800':
                            !canAdd,
                    }"
                    @click="
                        () => {
                            if (canAdd) {
                                props.cartItem.quantity++;
                            }
                        }
                    "
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
                            d="M12 4.5v15m7.5-7.5h-15"
                        />
                    </svg>
                </button>
            </div>

            <div class="flex flex-col items-end justify-between">
                <p class="text-xs text-gray-500 dark:text-gray-300">Subtotal</p>
                <p
                    class="text-sm font-semibold text-gray-900 dark:text-gray-300"
                >
                    Rp
                    {{
                        $formatCurrency(
                            props.cartItem.item_price * props.cartItem.quantity
                        )
                    }}
                </p>
            </div>
        </div>
    </div>
</template>
