<script setup lang="ts">
import BaseSection from "@/components/landing/BaseSection.vue";
import BaseButton from "@/components/landing/BaseButton.vue";
import BaseLayout from "@/layouts/BaseLayout.vue";
import { ref } from "vue";

const appTitle = ref(import.meta.env.VITE_APP_TITLE);

function scrollToTop() {
    document
        .getElementById("navbar")
        ?.scrollIntoView({ block: "start", behavior: "smooth" });
}
</script>

<template>
    <BaseLayout>
        <div class="w-full">
            <!-- Hero section -->
            <section id="hero" class="w-full pb-24">
                <BaseSection>
                    <div
                        class="flex items-center justify-center w-full col-span-12 px-6 mt-12 space-y-4 gap-x-16"
                    >
                        <div
                            class="w-full col-span-12 text-center xl:mt-10 sm:text-left sm:space-y-6 lg:col-span-6"
                        >
                            <br />
                            <h1
                                data-aos="fade-right"
                                data-aos-once="true"
                                class="text-[2.5rem] sm:text-5xl xl:text-5xl font-bold leading-tight sm:pr-8 xl:pr-10"
                            >
                                {{ appTitle }} <br />
                                <span class="text-header-gradient"
                                    >Elektronik</span
                                >
                                <br />
                                Anggaran Pendapatan dan Belanja Daerah
                            </h1>
                            <p
                                data-aos="fade-down"
                                data-aos-once="true"
                                data-aos-delay="300"
                                class="hidden paragraph sm:block"
                            >
                                Sistem yang membantu Pemerintah Daerah khususnya
                                Bidang Anggaran dalam menyimpan, melaporkan Data
                                APBD...
                            </p>

                            <br class="sm:hidden" />

                            <div
                                data-aos="fade-up"
                                data-aos-once="true"
                                data-aos-delay="700"
                                class="flex flex-col mt-2 space-y-4 sm:flex-row sm:space-y-0 sm:space-x-4"
                            >
                                <BaseButton
                                    @click="$route('login')"
                                    class="max-w-full font-medium px-8 py-4 bg-gradient-to-r from-[#468ef9] to-[#0c66ee] border border-[#0c66ee] text-white"
                                >
                                    Mulai
                                </BaseButton>
                                <!-- <BaseButton
                    class="max-w-full px-6 py-4 bg-inherit text-gradient border border-[#0c66ee] flex items-center justify-center"
                  >
                    <span>Download App</span>
                    <ChevronDownIcon :size="20" class="mt-1 text-[#0c66ee]" />
                  </BaseButton> -->
                            </div>
                        </div>
                        <div class="hidden col-span-6 md:block">
                            Logo
                            <!-- <img
                                data-aos="fade-up"
                                data-aos-delay="400"
                                src="@/assets/images/logo_papua_tengah.png"
                                class="w-3/4"
                                alt="Logo Pemda"
                            /> -->
                        </div>
                    </div>

                    <img
                        data-aos="fade-up"
                        data-aos-delay="300"
                        src="@/assets/img/pattern/ellipse-1.png"
                        class="absolute hidden w-6 sm:block bottom-12 xl:bottom-16 left-4 xl:left-0"
                    />
                    <img
                        data-aos="fade-up"
                        data-aos-delay="300"
                        src="@/assets/img/pattern/ellipse-2.png"
                        class="hidden sm:block absolute top-4 sm:top-10 right-64 sm:right-96 xl:right-[32rem] w-6"
                    />
                    <img
                        data-aos="fade-up"
                        data-aos-delay="300"
                        src="@/assets/img/pattern/ellipse-3.png"
                        class="absolute hidden w-6 sm:block bottom-56 right-24"
                    />
                    <img
                        data-aos="fade-up"
                        data-aos-delay="300"
                        src="@/assets/img/pattern/star.png"
                        class="hidden sm:block absolute top-20 sm:top-28 right-16 lg:right-0 lg:left-[30rem] w-8"
                    />
                </BaseSection>
            </section>
        </div>
    </BaseLayout>
</template>

<style scoped>
.text-header-gradient {
    background: rgb(57, 132, 244);
    background: linear-gradient(
        169.4deg,
        #3984f4 -6.01%,
        #0cd3ff 36.87%,
        #2f7cf0 78.04%,
        #0e65e8 103.77%
    );
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.max-h-0 {
    max-height: 0;
}
</style>
