<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import TextAreaInput from "@/Components/TextAreaInput.vue";
import { Link, useForm } from "@inertiajs/vue3";

const props = defineProps({ event: Object });

const form = useForm({
  name: props.event.name,
  description:  props.event.description,
  image: props.event.image,
  image_url: null,
  date:  props.event.date,
  registration_expires_at:  props.event.registration_expires_at,
  total_ticket:  props.event.total_ticket,
  ticket_info:  props.event.ticket_info,
});

const chooseImage = (ev) => {
  const file = ev.target.files[0];

  const reader = new FileReader();
  reader.onload = () => {
    form.image = file;

    form.image_url = reader.result;
  };
  reader.readAsDataURL(file);
};

const getTicketInfo = () => {
  return form.ticket_info;
};

const setTicketInfo = (category) => {
  form.ticket_info = category;
};

const addCategory = () => {
  regularTickets.value = false;
  setTicketInfo([...getTicketInfo(), { ticket_type: "", ticket_count: "" }]);
};

const removeCategory = (index) => {
  getTicketInfo().splice(index, 1);
};
</script>
<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        {{ props.event.name }}
      </h2>
    </template>
    <div class="py-12 px-12">
      <form>
        <div>
          <InputLabel for="name" value="name" />

          <TextInput
            id="name"
            type="text"
            class="mt-1 block w-full"
            v-model="form.name"
          />
        </div>
        <div class="mt-4">
          <InputLabel for="description" value="description" />

          <TextAreaInput
            id="description"
            class="mt-1 block w-full"
            v-model="form.description"
          />
        </div>
        <div class="mt-4">
          <img
            v-if="form.image"
            :src="form.image"
            :alt="form.name"
            class="w-64 h-48 object-cover"
          />
        </div>
        <div class="mt-4">
          <InputLabel for="date" value="due date" />

          <TextInput
            id="date"
            type="datetime-local"
            class="mt-1 block w-full"
            v-model="form.date"
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
            v-model="form.registration_expires_at"
          />
        </div>
        <div v-if="form.ticket_types" class="py-6 px-6">
          <span v-if="form.total_ticket != null" class="font-bold">Ticket Types</span>
          <div v-for="(option, index) in form.ticket_types" class="mt-4">
            <h3 class="text-lg font-bold">{{ index + 1 }}</h3>
            <TextInput
              id="ticket_name"
              type="text"
              class="mt-1 block w-full"
              placeholder="ticket name"
              v-model="option.ticket_type"
            />

            <TextInput
              id="ticket_count"
              type="number"
              class="mt-1 block w-full"
              v-model="option.ticket_count"
              placeholder="ticket number"
            />
          </div>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
