<script setup lang="ts">
import AdminDefaultLayout from "@/layouts/AdminDefaultLayout.vue";
import PageSection from "@/components/Sections/PageSection.vue";
import PaymentMethodTable from "@/pages/admin/main/payment_method/PaymentMethodTable.vue";
import CustomButton from "@/components/Forms/CustomButton.vue";
import { ref, watch } from "vue";
import CustomSearchBar from "@/components/Forms/CustomSearchBar.vue";

const tableData = ref(null);

const query = ref({
    limit: null,
    page: 1,
    search: null,
});

watch(
    () => query.value.search,
    () => {
        tableData.value?.getData(query.value);
    }
);
</script>

<template>
    <AdminDefaultLayout>
        <div data-aos="fade-up" data-aos-once="true">
            <PageSection
                :page-title="'Opsi'"
                id="pagetop"
                class="col-span-12 mb-4 text-sm xl:col-span-8"
            >
                <div class="flex items-center justify-center w-full gap-4">
                    <CustomSearchBar
                        v-model="query.search"
                        id="search"
                        placeholder="Cari paket"
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
            <PaymentMethodTable ref="tableData" />
        </div>
    </AdminDefaultLayout>
</template>
