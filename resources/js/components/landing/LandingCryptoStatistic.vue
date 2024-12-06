<script setup>
import LineChart from "@/components/landing/LineChart.vue";
</script>

<template>
  <div
    class="w-full mt-6 space-y-6 overflow-hidden lg:w-1/3 lg:mt-0"
    v-bind="$attrs"
  >
    <div class="flex items-center justify-between w-full">
      <span class="font-medium">{{ title }}</span>
      <button
        href="#"
        class="flex items-center px-3 py-1 space-x-1 text-sm font-medium text-blue-500 transition duration-300 rounded-md hover:bg-blue-50"
      >
        <span>More</span>
        <!-- <ChevronRightIcon :size="16" /> -->
      </button>
    </div>
    <div class="flex flex-col">
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div
          class="inline-block min-w-full px-2 py-2 overflow-hidden align-middle sm:px-6"
        >
          <table class="min-w-full">
            <thead>
              <tr>
                <th class="text-sm font-medium text-left text-gray-500">
                  Name
                </th>
                <th class="text-sm font-medium text-left text-gray-500">
                  Price
                </th>
                <th
                  class="hidden text-sm font-medium text-left text-gray-500 sm:block"
                >
                  Chart
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="data in datasets"
                :key="data.id"
                class="border-b border-gray-200"
              >
                <td class="py-4 whitespace-nowrap">
                  <div class="flex items-center space-x-2">
                    <img
                      :src="`./src/assets/img/crypto-icon/${data.logo}`"
                      alt=""
                    />
                    <span>{{ data.name }}</span>
                  </div>
                </td>
                <td class="py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <!-- <PlusThickIcon
                      v-if="data.increase"
                      :size="14"
                      class="text-emerald-500"
                    /> -->
                    <!-- <MinusThickIcon v-else :size="14" class="text-red-500" /> -->
                    <span>${{ data.price }}</span>
                  </div>
                </td>
                <td class="hidden sm:block whitespace-nowrap">
                  <div>
                    <!-- <LineChart
                      v-if="data.data && data.increase"
                      class="h-12 -mx-2 w-28"
                      :datasets="data.data"
                      :increase="data.increase"
                    /> -->
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "LandingCryptoStatistic",
  props: {
    title: {
      type: String,
      default: "",
    },
    datasets: {
      type: Array,
      default: () => [],
    },
  },
};
</script>
