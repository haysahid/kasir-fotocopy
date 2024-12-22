<script setup>
const props = defineProps({
  title: {
    type: String,
    default: "Attention",
  },
  description: {
    type: String,
    default: "Something went wrong.",
  },
});

const emit = defineEmits(["close"]);

function close() {
  emit("close");
}
</script>

<template>
  <div
    class="flex w-full border-l-6 border-warning bg-warning bg-opacity-[15%] px-4 py-2 shadow-md dark:bg-[#1B1B24] dark:bg-opacity-30 md:p-4"
  >
    <div
      class="flex items-center justify-center mr-5 rounded-lg h-9 w-9 bg-warning bg-opacity-30"
    >
      <svg
        width="19"
        height="16"
        viewBox="0 0 19 16"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          d="M1.50493 16H17.5023C18.6204 16 19.3413 14.9018 18.8354 13.9735L10.8367 0.770573C10.2852 -0.256858 8.70677 -0.256858 8.15528 0.770573L0.156617 13.9735C-0.334072 14.8998 0.386764 16 1.50493 16ZM10.7585 12.9298C10.7585 13.6155 10.2223 14.1433 9.45583 14.1433C8.6894 14.1433 8.15311 13.6155 8.15311 12.9298V12.9015C8.15311 12.2159 8.6894 11.688 9.45583 11.688C10.2223 11.688 10.7585 12.2159 10.7585 12.9015V12.9298ZM8.75236 4.01062H10.2548C10.6674 4.01062 10.9127 4.33826 10.8671 4.75288L10.2071 10.1186C10.1615 10.5049 9.88572 10.7455 9.50142 10.7455C9.11929 10.7455 8.84138 10.5028 8.79579 10.1186L8.13574 4.75288C8.09449 4.33826 8.33984 4.01062 8.75236 4.01062Z"
          fill="#FBBF24"
        ></path>
      </svg>
    </div>
    <div class="w-full">
      <div class="flex items-center justify-between gap-2">
        <h5 class="mb-1 text-lg font-bold text-[#9D5425]">{{ props.title }}</h5>
        <button
          @click="close"
          class="text-danger dark:text-gray-200 dark:hover:text-danger"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
            class="w-5 h-5"
          >
            <path
              d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"
            />
          </svg>
        </button>
      </div>
      <p class="leading-relaxed text-[#D0915C]">
        {{ props.description }}
      </p>
    </div>
  </div>
</template>
