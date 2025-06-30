<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import TextAreaInput from "@/Components/TextAreaInput.vue";
//import { Link } from "@inertiajs/vue3";

defineProps({ event: Object });

</script>
<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        {{ event.name }}
      </h2>
    </template>
    <div class="py-12 px-12">
      <form @submit.prevent="submit">
        <div>

          <InputLabel for="name" value="name" />

          <TextInput
            id="name"
            type="text"
            class="mt-1 block w-full"
            v-model="event.name"
            readonly
          />
        </div>
        <div class="mt-4">
          <InputLabel for="description" value="description" />

          <TextAreaInput
            id="description"
            class="mt-1 block w-full"
            v-model="event.description"
            autocomplete="description"
            readonly
          />
        </div>
        <div class="mt-4">
          <img
            v-if="event.image"
            :src="event.image"
            :alt="event.name"
            class="w-64 h-48 object-cover"
          />
        </div>
        <div class="mt-4">
          <InputLabel for="date" value="due date" />

          <TextInput
            id="date"
            type="datetime-local"
            class="mt-1 block w-full"
            v-model="event.date"
            readonly
          />
        </div>
        <div class="mt-4">
          <InputLabel
            for="registration expiration date"
            value="registration expiration date"
          />

          <TextInput
            id="registration expiration date"
            type="datetime-local"
            class="mt-1 block w-full"
            v-model="event.registration_expires_at"
            readonly
          />
        </div>
        <div v-if="event.ticket_types" class="py-6 px-6">
        <span v-if="event.total_ticket != null" class="font-bold">Ticket Types</span>
          <div v-for="(option, index) in event.ticket_types" class="mt-4">
            <h3 class="text-lg font-bold">{{ index + 1 }}</h3>
            <TextInput
              id="ticket_name"
              type="text"
              class="mt-1 block w-full"
              placeholder="ticket name"
              v-model="option.ticket_type"
              readonly
            />

            <TextInput
              id="ticket_count"
              type="number"
              class="mt-1 block w-full"
              v-model="option.ticket_count"
              placeholder="ticket number"
              readonly
            />

            <TextInput
              id="ticket_count"
              type="number"
              class="mt-1 block w-full"
              v-model="option.ticket_price"
              placeholder="ticket number"
              readonly
            />
          </div>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
