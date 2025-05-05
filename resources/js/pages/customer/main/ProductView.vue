<script setup lang="ts">
import DefaultLayout from "@/layouts/DefaultLayout.vue";
import PageSection from "@/components/Sections/PageSection.vue";
import ProductTable from "./product/ProductTable.vue";
import CustomButton from "@/components/Forms/CustomButton.vue";
import { ref, watch, computed } from "vue";
import CustomSearchBar from "@/components/Forms/CustomSearchBar.vue";
import SearchSelectGroup from "@/components/Forms/SearchSelectGroup.vue";
import { useCategoryStore } from "@/stores/category";

const tableData = ref(null);

const categoryStore = useCategoryStore();
const categoryDropdown = ref([]);

const query = ref({
    limit: null,
    page: 1,
    search: null,
    category_id: null,
});

async function getCategoryDropdown(search = null, limit = 5) {
    const response = await categoryStore.getDropdown(search, limit);
    categoryDropdown.value = response.result;
}

const getCategoryOptions = computed(() => {
    const options = categoryDropdown.value.map((item) => {
        return {
            value: item.id,
            text: item.name,
        };
    });

    return options;
});

watch(
    () => query.value.search,
    () => {
        tableData.value?.getData(query.value);
    }
);

watch(
    () => query.value.category_id,
    () => {
        tableData.value?.getData(query.value);
    }
);

getCategoryDropdown();
</script>

<template>
    <DefaultLayout>
        <div data-aos="fade-up" data-aos-once="true">
            <PageSection
                :page-title="'Produk / Jasa'"
                id="pagetop"
                class="col-span-12 mb-4 text-sm xl:col-span-8"
            >
                <div class="flex items-center justify-center w-full gap-4">
                    <SearchSelectGroup
                        id="role"
                        type="single"
                        placeholder="Pilih kategori"
                        :options="getCategoryOptions"
                        @search="getCategoryDropdown"
                        @select="query.category_id = $event"
                        @clear="query.category_id = null"
                    />

                    <CustomSearchBar
                        v-model="query.search"
                        id="search"
                        placeholder="Cari produk / jasa"
                        :useDebounce="true"
                    />

                    <div class="flex items-center justify-center gap-2">
                        <CustomButton
                            v-if="tableData && tableData.selectionMode"
                            :isFull="false"
                            color="bg-danger"
                            @click="tableData.showDeleteItemDialog()"
                        >
                            Hapus ({{ tableData.selectedItems.length }})
                        </CustomButton>

                        <CustomButton
                            v-if="tableData && !tableData.selectionMode"
                            :isFull="false"
                            @click="tableData.showItemFormDialog(null)"
                        >
                            Tambah
                        </CustomButton>
                    </div>
                </div>
            </PageSection>
            <ProductTable ref="tableData" />
        </div>
    </DefaultLayout>
</template>
