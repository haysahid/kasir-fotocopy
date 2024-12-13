<script setup lang="ts">
import DefaultLayout from "@/layouts/DefaultLayout.vue";
import PageSection from "@/components/Sections/PageSection.vue";
import ProductList from "./purchase/ProductList.vue";
import CustomSearchBar from "@/components/Forms/CustomSearchBar.vue";
import { ref, watch } from "vue";


</script>

<template>
    <DefaultLayout>
        <div data-aos="fade-up" data-aos-once="true">
            <ProductList ref="listData" />
        </div>
    </DefaultLayout>
</template>
