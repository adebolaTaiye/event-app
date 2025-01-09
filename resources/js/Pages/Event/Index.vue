<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link } from '@inertiajs/vue3'
//import EventListItem from "@/Components/EventListItem.vue";

defineProps({ events: Array });

function truncateDescription(description) {
  const words = description.split(' ');
  if (words.length > 10) {
    return words.slice(0, 10).join(' ') + '...';
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
      <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3">
        <div
          class="card bg-base-100 w-96 shadow-xl"
          v-for="(event, index) in events.data"
          :key="index"
        >
          <figure>
            <img :src="event.image" alt="image" class="card-image"/>
          </figure>
          <div class="card-body">
            <h2 class="card-title">{{ event.name }}</h2>
            <p>{{ truncateDescription(event.description) }}</p>
            <div class="card-actions justify-end">
                <button
            class=" btn btn-error text-white"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="w-6 h-6"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
              />
            </svg>
            Delete
          </button>
            <button class="btn btn-success text-white">Edit</button>
              <Link :href="route('event.show',event.id)" class="btn btn-primary text-white" as="button">Show</Link>
            </div>
          </div>
        </div>
      </div>
      <div class="text-center mt-8">
    <Link v-for="(link, index) in events.links"
   :key="index"
   :href="link.url"
    class=" relative inline-flex items-center px-4 py-3 mb-7 border font-medium whitespace-nonwrap"
    :class="[
      link.active
      ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
      : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
      index === 0 ? 'rounded-l-md' : '',
      index === events.links.length - 1 ? 'rounded-r-md' : ''
      ]"
   v-html="link.label"  >
   </Link>
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
