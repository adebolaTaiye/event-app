<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import EventRegistration from "@/Components/EventRegistration.vue";

defineProps({ event: Object});
</script>
<template>
  <AuthenticatedLayout>
    <EventRegistration :types ="event.ticket_types" :event="event"/>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        {{ event.name }}
      </h2>
    </template>
      <div class="py-4 px-7">
        <img
          v-if="event.image"
          :src="event.image"
          :alt="event.name"
          class="w-full h-64 object-cover"
        />
      </div>
      <div class="py-2 px-9">
      <div class="mt-4">
        <div class="font-bold text-2xl mb-2">Date and time</div>
        <div>{{ event.date }}</div>
      </div>
      <div class="mt-4">
        <div class="font-bold text-2xl mb-2">About this event</div>
        <div>{{ event.description }}</div>
      </div>
      <div class="mt-4">
        <div class="font-bold text-2xl mb-2">Registration End Date</div>
        <div>{{ event.registration_expires_at }}</div>
      </div>
    </div>
     <div  class="fixed right-0 left-0 bottom-0 bg-white p-2 flex justify-center align-center mt-4 dark:bg-gray-800">
        <label for="my_modal_2" class="btn bg-amber-600 rounded py-2 px-2 w-96 text-white "> Get Tickets!</label>
    </div>

  </AuthenticatedLayout>
</template>
<style scoped></style>
