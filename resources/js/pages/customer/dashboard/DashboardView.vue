<script setup lang="ts">
import DefaultLayout from "@/layouts/DefaultLayout.vue";
</script>

<template>
    <main>
        <DefaultLayout>
            <div data-aos="fade-up" data-aos-once="true">
                <PageSection
                    :page-title="'Hello,'"
                    id="pagetop"
                    class="col-span-12 text-sm xl:col-span-8"
                >
                </PageSection>

                <p
                    class="mt-1 font-semibold text-black text-title-sm dark:text-white"
                >
                    Selamat datang!
                </p>
            </div>
        </DefaultLayout>
    </main>
</template>
