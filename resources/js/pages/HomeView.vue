<script setup lang="ts">
import BaseSection from "@/components/landing/BaseSection.vue";
import BaseButton from "@/components/landing/BaseButton.vue";
import BaseLayout from "@/layouts/BaseLayout.vue";
import { ref } from "vue";
import { useConfigStore } from "@/stores/config";
import { Link } from "@inertiajs/inertia-vue3";
import SubscriptionPlans from "./customer/public/SubscriptionPlans.vue";

const configStore = useConfigStore();

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
                            <h1
                                data-aos="fade-right"
                                data-aos-once="true"
                                class="text-[2.5rem] sm:text-5xl xl:text-5xl font-bold leading-tight sm:pr-8 xl:pr-10"
                            >
                                <span class="text-header-gradient">
                                    {{ configStore.title }}
                                </span>
                            </h1>

                            <p
                                class="text-2xl text-slate-800 dark:text-gray-400 max-sm:mt-2"
                            >
                                Sistem Pengelolaan Kasir Usaha Ritel
                            </p>

                            <p
                                data-aos="fade-down"
                                data-aos-once="true"
                                data-aos-delay="300"
                                class="paragraph max-sm:mt-4 sm:max-w-[30rem] dark:text-gray-500"
                            >
                                Aplikasi kasir yang memudahkan pengelolaan usaha
                                ritel Anda. Dengan fitur yang lengkap dan mudah
                                digunakan.
                            </p>

                            <br />

                            <div
                                data-aos="fade-up"
                                data-aos-once="true"
                                data-aos-delay="700"
                                class="flex flex-wrap justify-center gap-y-4 gap-x-4 sm:justify-start"
                            >
                                <Link :href="route('login')">
                                    <BaseButton
                                        class="max-w-full font-medium px-12 py-4 bg-gradient-to-r from-[#468ef9] to-[#0c66ee] border border-[#0c66ee] text-gray-200"
                                    >
                                        Mulai
                                    </BaseButton>
                                </Link>
                                <a
                                    href="https://wa.me/6285727320485"
                                    target="_blank"
                                >
                                    <BaseButton
                                        class="flex items-center justify-center max-w-full gap-2 px-6 py-4 text-green-600 border border-green-600 bg-inherit hover:shadow-green-600/50"
                                    >
                                        <svg
                                            fill="#000000"
                                            version="1.1"
                                            id="Capa_1"
                                            xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink"
                                            viewBox="0 0 30.667 30.667"
                                            xml:space="preserve"
                                            class="size-5 fill-green-600"
                                        >
                                            <g>
                                                <path
                                                    d="M30.667,14.939c0,8.25-6.74,14.938-15.056,14.938c-2.639,0-5.118-0.675-7.276-1.857L0,30.667l2.717-8.017
                                                c-1.37-2.25-2.159-4.892-2.159-7.712C0.559,6.688,7.297,0,15.613,0C23.928,0.002,30.667,6.689,30.667,14.939z M15.61,2.382
                                                c-6.979,0-12.656,5.634-12.656,12.56c0,2.748,0.896,5.292,2.411,7.362l-1.58,4.663l4.862-1.545c2,1.312,4.393,2.076,6.963,2.076
                                                c6.979,0,12.658-5.633,12.658-12.559C28.27,8.016,22.59,2.382,15.61,2.382z M23.214,18.38c-0.094-0.151-0.34-0.243-0.708-0.427
                                                c-0.367-0.184-2.184-1.069-2.521-1.189c-0.34-0.123-0.586-0.185-0.832,0.182c-0.243,0.367-0.951,1.191-1.168,1.437
                                                c-0.215,0.245-0.43,0.276-0.799,0.095c-0.369-0.186-1.559-0.57-2.969-1.817c-1.097-0.972-1.838-2.169-2.052-2.536
                                                c-0.217-0.366-0.022-0.564,0.161-0.746c0.165-0.165,0.369-0.428,0.554-0.643c0.185-0.213,0.246-0.364,0.369-0.609
                                                c0.121-0.245,0.06-0.458-0.031-0.643c-0.092-0.184-0.829-1.984-1.138-2.717c-0.307-0.732-0.614-0.611-0.83-0.611
                                                c-0.215,0-0.461-0.03-0.707-0.03S9.897,8.215,9.56,8.582s-1.291,1.252-1.291,3.054c0,1.804,1.321,3.543,1.506,3.787
                                                c0.186,0.243,2.554,4.062,6.305,5.528c3.753,1.465,3.753,0.976,4.429,0.914c0.678-0.062,2.184-0.885,2.49-1.739
                                                C23.307,19.268,23.307,18.533,23.214,18.38z"
                                                />
                                            </g>
                                        </svg>
                                        <span> Hubungi Kami </span>
                                    </BaseButton>
                                </a>
                            </div>
                        </div>
                        <div class="hidden w-1/2 col-span-6 md:block">
                            <img
                                data-aos="fade-up"
                                data-aos-delay="400"
                                src="@/assets/images/illustration/illustration-02.svg"
                                alt="Illustration"
                            />
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

            <!-- Subsription Plan -->
            <section>
                <SubscriptionPlans />
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
