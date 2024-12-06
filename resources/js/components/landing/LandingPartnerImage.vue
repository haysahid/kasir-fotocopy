<template>
  <div>
    <img
      :src="`./src/assets/img/partner/${img}`"
      class="mx-auto sm:w-1/2 lg:w-72"
      alt=""
    />
  </div>
</template>
<script>
export default {
  props: {
    img: {
      type: String,
      default: "",
    },
  },
};
</script>
