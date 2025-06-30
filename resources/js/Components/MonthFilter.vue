<script setup>
import { router } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const selectedMonth = ref("");
const months = ref([
  "January",
  "February",
  "March",
  "April",
  "May",
  "June",
  "July",
  "August",
  "September",
  "October",
  "November",
  "December",
]);

watch(selectedMonth, (value) => {
  router.get("/index", { filter: { month: value } }, { preserveState: true });
});
</script>
<template>
    <div class="flex justify-end mb-2 ml-4">
        <select class="select" v-model="selectedMonth" @change="monthFilter" placeholder="month filter">
          <option v-for="(month, index) in months" :key="index" :value="index + 1" >
            {{ month }}
          </option>
        </select>
      </div>
</template>
<style  scoped>

</style>
