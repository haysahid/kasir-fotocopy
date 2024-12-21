<script setup>
import BaseSection from "@/components/landing/BaseSection.vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import SubscriptionPlanItem from "./SubscriptionPlanItem.vue";
import { usePlanStore } from "@/stores/plan";

const planStore = usePlanStore();

planStore.getDropdown();

function selectPlan(plan) {
    window.location.href = route("subscription.detail", {
        id: plan.id,
    });
}

function translateDurationType(type) {
    switch (type) {
        case "days":
            return "hari";
        case "months":
            return "bulan";
        case "years":
            return "tahun";
        default:
            return "";
    }
}

function getDurationText(duration, type) {
    if (duration === 1) {
        return translateDurationType(type);
    }

    return duration + " " + translateDurationType(type);
}
</script>

<template>
    <div class="w-full max-w-screen-xl mx-auto">
        <div
            data-aos="fade-up"
            data-aos-once="true"
            data-aos-delay="150"
            class="flex flex-col items-center justify-center h-full px-12"
        >
            <h1
                class="text-3xl font-bold text-center text-header-gradient dark:text-white"
            >
                Pilih Paket Berlangganan
            </h1>
            <p class="mt-4 text-center text-gray-500">
                Pilih paket berlangganan yang sesuai dengan kebutuhan Anda.
            </p>
        </div>

        <div class="flex flex-wrap justify-center gap-6 p-12 mt-4">
            <DefaultCard
                v-for="plan in planStore.dropdown"
                data-aos="fade-up"
                data-aos-once="true"
                data-aos-delay="200"
                class="px-5 pt-6 pb-5 rounded-3xl w-full sm:w-[260px]"
                :class="{
                    ' !border-secondary': plan.priority,
                }"
            >
                <SubscriptionPlanItem
                    :title="plan.name"
                    :description="plan.description"
                    :price="plan.price"
                    :duration="
                        getDurationText(plan.duration, plan.duration_type)
                    "
                    :options="plan.options"
                    :priority="plan.priority"
                    @select="() => selectPlan(plan)"
                />
            </DefaultCard>
        </div>
    </div>
</template>
