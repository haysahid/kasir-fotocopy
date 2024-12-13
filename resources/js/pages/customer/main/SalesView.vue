<script setup lang="ts">
import DefaultLayout from "@/layouts/DefaultLayout.vue";
import AddSales from "./sales/AddSales.vue";
</script>

<template>
    <DefaultLayout>
        <div data-aos="fade-up" data-aos-once="true">
            <AddSales ref="listData" />
        </div>
    </DefaultLayout>
</template>
