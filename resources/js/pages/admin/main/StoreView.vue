<script setup lang="ts">
import AdminDefaultLayout from "@/layouts/AdminDefaultLayout.vue";
import PageSection from "@/components/Sections/PageSection.vue";
import UserTable from "@/pages/admin/main/user/UserTable.vue";
import CustomButton from "@/components/Forms/CustomButton.vue";
import { onMounted, ref, watch, computed } from "vue";
import CustomSearchBar from "@/components/Forms/CustomSearchBar.vue";
import SelectGroup from "@/components/Forms/SelectGroup.vue";
import SearchSelectGroup from "@/components/Forms/SearchSelectGroup.vue";
import { useRoleStore } from "@/stores/admin/role";
import StoreTable from "./store/StoreTable.vue";

const tableData = ref(null);

const roleStore = useRoleStore();

const roleDropdown = ref([]);

const query = ref({
    limit: null,
    page: 1,
    search: null,
    role_id: null,
});

async function getRoleDropdown(search = null) {
    const response = await roleStore.getDropdown(search);
    roleDropdown.value = response.result;
}

const getRoleOptions = computed(() => {
    const options = roleDropdown.value.map((item) => {
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
    () => query.value.role_id,
    () => {
        tableData.value?.getData(query.value);
    }
);

getRoleDropdown();
</script>

<template>
    <AdminDefaultLayout>
        <div data-aos="fade-up" data-aos-once="true">
            <PageSection
                :page-title="'Toko'"
                id="pagetop"
                class="col-span-12 mb-4 text-sm xl:col-span-8"
            >
                <div class="flex items-center justify-center w-full gap-4">
                    <!-- <SelectGroup
                        v-model="query.role_id"
                        id="role_id"
                        :options="getRoleOptions"
                    /> -->

                    <SearchSelectGroup
                        id="role_id"
                        type="single"
                        placeholder="Pilih role"
                        :options="getRoleOptions"
                        @search="getRoleDropdown"
                        @select="query.role_id = $event"
                        @clear="query.role_id = null"
                    />

                    <CustomSearchBar
                        v-model="query.search"
                        id="search"
                        placeholder="Cari toko"
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

            <StoreTable ref="tableData" />
        </div>
    </AdminDefaultLayout>
</template>
