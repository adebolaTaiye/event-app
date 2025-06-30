<script setup>
import { useForm } from "@inertiajs/vue3";
import { computed, watch } from "vue";
import Paystack from '@paystack/inline-js'


const props = defineProps({
  types: Array,
  event: Object,
  access_code: String,
});

const form1 = useForm({
  event_id: props.event.id,
  ticket_type_id: "",
  quantity: 1,
  total: 0
});

// Computed property to calculate the total dynamically
const totalPrice = computed(() => {
  const selectedType = props.types.find(type => type.id === form1.ticket_type_id);
  return selectedType ? selectedType.ticket_price * form1.quantity : 0;
});

// Watcher to update form1.total whenever totalPrice changes
watch(totalPrice, (newTotal) => {
  form1.total = newTotal;
});

const form2 = useForm({
  event_id: props.event.id,
});

const submitForTicket = () => {
  form1.post(route("user.payment"), {
    onFinish: () => form1.reset("ticket_type_id", "quantity"),
  });
};

const submitForFree = () => {
  form2.post(route("user.register"));
};

const popup = new Paystack()

if(props.access_code === undefined ){
    //
    console.log('yyyyy')
}else {
    console.log(props.access_code)
    popup.resumeTransaction(props.access_code)
}

</script>
<template>
    <input type="checkbox" id="my_modal_2" class="modal-toggle" />
    <div class="modal" role="dialog">
      <div class="modal-box">
        <!-- Flash Message -->
        <div
          role="alert"
          class="alert alert-info text-center mt-2"
          v-if="$page.props.flash.message2"
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
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          <span>{{ $page.props.flash.message2 }}</span>
        </div>
        <!-- Registration Deadline -->
        <div class="text-sm text-gray-500 mb-4 text-center">
          <strong>Registration ends on:</strong> {{ event.registration_expires_at }}
        </div>

        <!-- Ticket Booking Form -->
        <div v-if="props.types.length > 0">
          <form @submit.prevent="submitForTicket" class="space-y-4">
            <!-- Ticket Type Selection -->
            <div>
              <label class="font-bold block mb-2">Select Ticket Type:</label>
              <div
                v-for="(type, index) in props.types"
                :key="index"
                class="flex items-center mb-2"
              >
                <input
                  type="radio"
                  class="radio radio-sm radio-warning"
                  :value="type.id"
                  v-model="form1.ticket_type_id"
                />
                <span class="ml-2 font-medium">{{ type.ticket_type }}</span>
                <span class="ml-4 text-gray-600">
                  Price: N{{ type.ticket_price}}
                </span>
              </div>
            </div>

            <!-- Quantity Input -->
            <div>
              <label class="font-bold block mb-2">Quantity:</label>
              <input
                type="number"
                v-model.number="form1.quantity"
                class="input input-warning input-md w-full"
                min="1"
                placeholder="Enter quantity"
              />
            </div>
            <div class="flex">
                <span class="font-bold block mb-2 mr-2">Total:</span> N{{totalPrice}}
            </div>
            <!-- Submit Button -->
            <div>
              <button class="btn bg-amber-600 rounded text-white w-full">
                Register
              </button>
            </div>
          </form>
        </div>

        <!-- Free Registration Form -->
        <div v-else>
          <form @submit.prevent="submitForFree">
            <div class="mt-4">
              <button class="btn bg-amber-600 rounded text-white w-full">
                Register for Free
              </button>
            </div>
          </form>
        </div>
      </div>
      <label class="modal-backdrop" for="my_modal_2">Close</label>
    </div>
  </template>

<style scoped></style>
