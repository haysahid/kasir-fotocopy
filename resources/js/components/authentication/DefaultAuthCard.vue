<script setup lang="ts">
const props = defineProps([
    "subtitle",
    "title",
    "customClasses",
    "centerTitle",
    "centerContent",
]);
</script>

<template>
    <!-- ====== Forms Section Start -->
    <div
        class="bg-white border rounded-xl max-sm:w-[90%] max-md:w-[60%] max-xl:w-1/2 border-stroke shadow-default dark:border-strokedark dark:bg-boxdark duration-300 ease-linear"
    >
        <div
            class="flex flex-wrap"
            :class="{
                'items-center': props.centerContent,
            }"
        >
            <div class="hidden w-full xl:block xl:w-1/2">
                <div class="py-17.5 px-26 text-center">
                    <p class="font-medium 2xl:px-15 dark:text-gray-200">
                        Aplikasi Fotocopy
                    </p>

                    <span class="inline-block mt-15">
                        <img
                            src="@/assets/images/illustration/illustration-02.svg"
                            alt="illustration"
                        />
                    </span>
                </div>
            </div>
            <div
                class="w-full border-stroke dark:border-strokedark xl:w-1/2 xl:border-l-2"
            >
                <div class="w-full p-4 sm:p-10">
                    <span class="mb-1.5 block font-medium">{{
                        props.subtitle
                    }}</span>
                    <h2
                        class="text-2xl font-bold text-black mb-9 dark:text-white sm:text-title-lg"
                        :class="{
                            'text-center': props.centerTitle,
                        }"
                    >
                        {{ props.title }}
                    </h2>

                    <slot></slot>
                </div>
            </div>
        </div>
    </div>
    <!-- ====== Forms Section End -->
</template>
