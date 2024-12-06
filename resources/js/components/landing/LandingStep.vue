<template>
  <div class="max-w-[280px] xl:max-w-[363px] space-y-6 sm:space-y-3 text-center">
    <img :src="`./src/assets/img/getting-started/${step.img}`" class="max-w-[245px] mx-auto" alt="" />
    <h3 class="text-xl font-semibold text-neutral-800">{{ step.title }}</h3>
    <p class="text-sm leading-relaxed text-gray-700">
      {{ step.description }}
    </p>
  </div>
</template>
<script>
export default {
  name: 'LandingStep',
  props: {
    step: {
      type: Object,
      required: true,
    },
  },
}
</script>
