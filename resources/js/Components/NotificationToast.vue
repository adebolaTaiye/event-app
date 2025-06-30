<template>
  <div>
    <div
      role="alert"
      class="alert alert-success text-white text-center mx-auto max-w-3xl z-20 mt-4 fixed right-0 left-0 shadow-lg"
      v-if="$page.props.flash.message"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-6 w-6 shrink-0 stroke-current"
        fill="none"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
        >
        </path>
      </svg>
      <span>{{ $page.props.flash.message }}</span>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted} from 'vue'

    const showMessage = ref(false)

    onMounted(() => {
        showMessage.value = true
    })
        setTimeout(() => {
            showMessage.value = false
        }, 3000);

</script>

<style lang="scss" scoped></style>
