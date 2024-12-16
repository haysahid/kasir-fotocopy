<script setup>
import BaseSection from "@/components/landing/BaseSection.vue";
import DefaultCard from "@/components/Forms/DefaultCard.vue";
import SubscriptionPlanItem from "./SubscriptionPlanItem.vue";
</script>

<template>
    <div class="w-full max-w-screen-xl mx-auto">
        <div
            data-aos="fade-up"
            data-aos-once="true"
            data-aos-delay="200"
            class="flex flex-col items-center justify-center h-full"
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

        <div class="grid grid-cols-3 gap-4 p-12 mt-8">
            <DefaultCard
                data-aos="fade-up"
                data-aos-once="true"
                data-aos-delay="300"
            >
                <SubscriptionPlanItem
                    title="Paket Basic"
                    description="Paket berlangganan untuk usaha kecil."
                    :price="100000"
                />
            </DefaultCard>

            <DefaultCard
                data-aos="fade-up"
                data-aos-once="true"
                data-aos-delay="400"
            >
                <SubscriptionPlanItem
                    title="Paket Medium"
                    description="Paket berlangganan untuk usaha menengah."
                    :price="200000"
                />
            </DefaultCard>

            <DefaultCard
                data-aos="fade-up"
                data-aos-once="true"
                data-aos-delay="500"
            >
                <SubscriptionPlanItem
                    title="Paket Large"
                    description="Paket berlangganan untuk usaha besar."
                    :price="300000"
                />
            </DefaultCard>
        </div>
    </div>
</template>
