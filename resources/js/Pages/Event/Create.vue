<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import TextAreaInput from "@/Components/TextAreaInput.vue";
import Checkbox from "@/Components/Checkbox.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const form = useForm({
  name: "",
  description: "",
  image: "",
  image_url: null,
  date: "",
  registration_expires_at: "",
  total_ticket: "",
  ticket_info: [],
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

let regularTickets = ref(false);

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

const submit = () => {
  form.post(route("event.store"));
};
</script>

<template>
  <Head title="Event" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        Add Event
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
            v-model="form.name"
            autofocus
            autocomplete="name"
          />

          <InputError class="mt-2" :message="form.errors.name" />
        </div>
        <div class="mt-4">
          <InputLabel for="description" value="description" />

          <TextAreaInput
            id="description"
            class="mt-1 block w-full"
            v-model="form.description"
            autocomplete="description"
          />

          <InputError class="mt-2" :message="form.errors.description" />
        </div>
        <div class="mt-4">
          <img
            v-if="form.image"
            :src="form.image_url"
            :alt="form.name"
            class="w-64 h-48 object-cover"
          />
        </div>
        <div class="mt-4">
          <InputLabel for="image" value="upload image" />

          <TextInput
            id="image"
            type="file"
            class="mt-1 block w-full"
            @change="chooseImage"
          />

          <InputError class="mt-2" :message="form.errors.image" />
        </div>

        <div class="mt-4">
          <InputLabel for="date" value="due date" />

          <TextInput
            id="date"
            type="datetime-local"
            class="mt-1 block w-full"
            v-model="form.date"
          />

          <InputError class="mt-2" :message="form.errors.date" />
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

          <InputError class="mt-2" :message="form.errors.registration_expires_at" />
        </div>
        <!-- <div class="mt-4">
            <Checkbox @click="regularTickets = !regularTickets" />
            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400"
              >add ticket number to the event</span
            >
          </div> -->
        <div class="mt-4">
          <button
            type="button"
            class="flex ms-2 text-sm text-gray-200 bg-gray-400 p-2 rounded"
            @click="addCategory"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="w-4 h-4"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 4.5v15m7.5-7.5h-15"
              />
            </svg>
            add ticket information
          </button>
        </div>
        <div v-for="(option, index) in form.ticket_info" class="mt-4">
          <h3 class="text-lg font-bold">{{ index + 1 }}</h3>
          <TextInput
            id="ticket_name"
            type="text"
            class="mt-1 block w-full"
            placeholder="ticket name"
            v-model="option.ticket_type"
            required
          />

          <TextInput
            id="ticket_count"
            type="number"
            class="mt-1 block w-full"
            v-model="option.ticket_count"
            placeholder="ticket number"
            required
          />
          <button
            @click="removeCategory(index)"
            class="flex items-center text-xs py-1 px-3 mr-2 rounded-sm text-red-500 hover:border-red-500"
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
        </div>

        <div class="mt-4">
          <button
            type="submit"
            class="mt-2 bg-blue-400 text-white border rounded p-2"
            :disabled="form.processing"
          >
            Submit
          </button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

<style lang="scss" scoped></style>
