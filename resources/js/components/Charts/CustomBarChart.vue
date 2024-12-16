<script setup lang="ts">
import { ref, onUpdated, onMounted } from "vue";
// @ts-ignore
import VueApexCharts from "vue3-apexcharts";
import DefaultCard from "@/components/Forms/DefaultCard.vue";

const props = defineProps({
    height: {
        type: String,
        default: "335",
    },
    showCard: {
        type: Boolean,
        default: true,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: "Belanjaan",
    },
    chartData: {
        type: Object as () => ChartData,
        default: {
            series: [
                {
                    name: "Belanja",
                    data: [0, 0, 0, 0, 0, 0, 0],
                },
            ],
            labels: ["-", "-", "-", "-", "-", "-", "-"],
        },
    },
});

interface ChartData {
    series: {
        name: string;
        data: number[];
    }[];
    labels: string[];
}

const chart = ref(null);

const apexOptions = ref({
    colors: ["#3056D3", "#80CAEE"],
    chart: {
        type: "bar",
        height: 335,
        stacked: true,
        toolbar: {
            show: false,
        },
        zoom: {
            enabled: true,
            type: "x",
            resetIcon: {
                offsetX: -10,
                offsetY: 0,
                fillColor: "#fff",
                strokeColor: "#37474F",
            },
            selection: {
                background: "#90CAF9",
                border: "#0D47A1",
            },
        },
    },
    responsive: [
        {
            breakpoint: 1536,
            options: {
                plotOptions: {
                    bar: {
                        borderRadius: 0,
                        columnWidth: "25%",
                    },
                },
            },
        },
    ],
    plotOptions: {
        bar: {
            horizontal: false,
            borderRadius: 0,
            columnWidth: "25%",
            borderRadiusApplication: "end",
            borderRadiusWhenStacked: "last",
        },
    },
    dataLabels: {
        enabled: false,
    },
    xaxis: {
        type: "category",
        categories: [],
        labels: {
            rotateAlways: false,
            hideOverlappingLabels: false,
            trim: true,
        },
    },
    yaxis: {
        labels: {
            formatter: (value) => {
                return Intl.NumberFormat("id-ID").format(value);
            },
        },
    },

    legend: {
        position: "top",
        horizontalAlign: "left",
        fontFamily: "Satoshi",
        fontWeight: 500,
        fontSize: "14px",

        markers: {
            radius: 99,
        },
    },
    fill: {
        opacity: 1,
    },
});

const series = ref(null);

onUpdated(() => {
    apexOptions.value.xaxis.categories = props.chartData.labels;
    series.value = props.chartData.series;
});

onMounted(() => {
    apexOptions.value.xaxis.categories = props.chartData.labels;
    series.value = props.chartData.series;
});
</script>

<template>
    <DefaultCard :showShadow="false" :showBorder="false" class="px-6 py-8">
        <div
            class="justify-between gap-4 mb-4 transition-all duration-200 ease-in-out sm:flex"
        >
            <div>
                <h4 class="text-xl font-bold text-black dark:text-white">
                    {{ props.title }}
                </h4>
            </div>
            <div>
                <!-- <div class="relative z-20 inline-block">
          <select
            name="#"
            id="#"
            class="relative z-20 inline-flex py-1 pl-3 pr-8 text-sm font-medium bg-transparent outline-none appearance-none"
          >
            <option value="">This Week</option>
            <option value="">Last Week</option>
          </select>
          <span class="absolute z-10 -translate-y-1/2 top-1/2 right-3">
            <svg
              width="10"
              height="6"
              viewBox="0 0 10 6"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M0.47072 1.08816C0.47072 1.02932 0.500141 0.955772 0.54427 0.911642C0.647241 0.808672 0.809051 0.808672 0.912022 0.896932L4.85431 4.60386C4.92785 4.67741 5.06025 4.67741 5.14851 4.60386L9.09079 0.896932C9.19376 0.793962 9.35557 0.808672 9.45854 0.911642C9.56151 1.01461 9.5468 1.17642 9.44383 1.27939L5.50155 4.98632C5.22206 5.23639 4.78076 5.23639 4.51598 4.98632L0.558981 1.27939C0.50014 1.22055 0.47072 1.16171 0.47072 1.08816Z"
                fill="#637381"
              />
              <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M1.22659 0.546578L5.00141 4.09604L8.76422 0.557869C9.08459 0.244537 9.54201 0.329403 9.79139 0.578788C10.112 0.899434 10.0277 1.36122 9.77668 1.61224L9.76644 1.62248L5.81552 5.33722C5.36257 5.74249 4.6445 5.7544 4.19352 5.32924C4.19327 5.32901 4.19377 5.32948 4.19352 5.32924L0.225953 1.61241C0.102762 1.48922 -4.20186e-08 1.31674 -3.20269e-08 1.08816C-2.40601e-08 0.905899 0.0780105 0.712197 0.211421 0.578787C0.494701 0.295506 0.935574 0.297138 1.21836 0.539529L1.22659 0.546578ZM4.51598 4.98632C4.78076 5.23639 5.22206 5.23639 5.50155 4.98632L9.44383 1.27939C9.5468 1.17642 9.56151 1.01461 9.45854 0.911642C9.35557 0.808672 9.19376 0.793962 9.09079 0.896932L5.14851 4.60386C5.06025 4.67741 4.92785 4.67741 4.85431 4.60386L0.912022 0.896932C0.809051 0.808672 0.647241 0.808672 0.54427 0.911642C0.500141 0.955772 0.47072 1.02932 0.47072 1.08816C0.47072 1.16171 0.50014 1.22055 0.558981 1.27939L4.51598 4.98632Z"
                fill="#637381"
              />
            </svg>
          </span>
        </div> -->
            </div>
        </div>

        <div
            class="transition-all duration-200 ease-in-out"
            :class="{
                'opacity-10': props.loading,
            }"
        >
            <div id="chartTwo" class="-ml-5 -mb-9">
                <VueApexCharts
                    v-if="series"
                    type="bar"
                    :height="height"
                    :options="apexOptions"
                    :series="series"
                    ref="chart"
                />
            </div>
        </div>
    </DefaultCard>
</template>
