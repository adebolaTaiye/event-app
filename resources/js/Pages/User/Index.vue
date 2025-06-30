<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import MonthFilter from "@/Components/MonthFilter.vue";
import { Link } from "@inertiajs/vue3";

defineProps({ events: Array });

function truncateDescription(description) {
  const words = description.split(" ");
  if (words.length > 10) {
    return words.slice(0, 10).join(" ") + "...";
  }
  return description;
}
</script>
<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        Events
      </h2>
    </template>
    <div class="py-12 px-12">
      <MonthFilter />
      <div v-if="events.data.length <= 0">
        <div class="text-center font-medium text-4xl text-gray-700 dark:text-white">
          OOPs! No results found
        </div>
        
      </div>
      <div v-else>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3">
          <div
            class="card bg-base-100 w-82 shadow-xl"
            v-for="(event, index) in events.data"
            :key="index"
          >
            <figure>
              <img :src="event.image" alt="image" class="card-image" />
            </figure>
            <div class="card-body">
              <h2 class="card-title">{{ event.name }}</h2>
              <p>{{ truncateDescription(event.description) }}</p>
              <p><span class="font-bold mr-2">Date:</span>{{ event.date }}</p>
              <div class="card-actions justify-end">
                <Link
                  :href="route('user.show', event.id)"
                  class="btn btn-ghost text-sky-700"
                  as="button"
                  >Show</Link
                >
              </div>
            </div>
          </div>
        </div>
        <div class="text-center mt-8">
          <Link
            v-for="(link, index) in events.meta.links"
            :key="index"
            :href="link.url"
            class="relative inline-flex items-center px-4 py-3 mb-7 border font-medium whitespace-nonwrap"
            :class="[
              link.active
                ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-gray-800',
              index === 0 ? 'rounded-l-md' : '',
              index === events.links.length - 1 ? 'rounded-r-md' : '',
            ]"
            v-html="link.label"
          >
          </Link>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.card-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
}
</style>
