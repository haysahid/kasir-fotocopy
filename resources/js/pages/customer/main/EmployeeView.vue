<script setup lang="ts">
import DefaultLayout from "@/layouts/DefaultLayout.vue";
import PageSection from "@/components/Sections/PageSection.vue";
import EmployeeTable from "@/pages/customer/main/employee/EmployeeTable.vue";
import CustomButton from "@/components/Forms/CustomButton.vue";
import { onMounted, ref, watch, computed } from "vue";
import CustomSearchBar from "@/components/Forms/CustomSearchBar.vue";

const tableData = ref(null);

const query = ref({
    limit: null,
    page: 1,
    search: null,
    role_id: null,
});

watch(
    () => query.value.search,
    () => {
        tableData.value?.getData(query.value);
    }
);
</script>

<template>
    <DefaultLayout>
        <div data-aos="fade-up" data-aos-once="true">
            <PageSection
                :page-title="'Karyawan'"
                id="pagetop"
                class="col-span-12 mb-4 text-sm xl:col-span-8"
            >
                <div class="flex items-center justify-center w-full gap-4">
                    <CustomSearchBar
                        v-model="query.search"
                        id="search"
                        placeholder="Cari karyawan"
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
            <EmployeeTable ref="tableData" />
        </div>
    </DefaultLayout>
</template>
