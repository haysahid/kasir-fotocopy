<script setup lang="ts">
import DefaultLayout from "@/layouts/DefaultLayout.vue";
import AddPurchase from "./purchase/AddPurchase.vue";
</script>

<template>
    <DefaultLayout>
        <div data-aos="fade-up" data-aos-once="true">
            <AddPurchase ref="listData" />
        </div>
    </DefaultLayout>
</template>
