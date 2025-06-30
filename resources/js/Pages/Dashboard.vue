<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import OrganiserDashboardLayout from "@/Components/OrganiserDashboardLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";

defineProps({
  events: Array,
  eventCount: String,
  totalRevenue: String,
  totalRegistrationsCount: String,
});
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        Dashboard
      </h2>
    </template>
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
          <div v-if="$page.props.auth.user.role == 'organizer'">
            <div class="p-6 text-gray-900 dark:text-gray-100">
              <OrganiserDashboardLayout
                :eventCount="eventCount"
                :totalRevenue="totalRevenue"
                :totalRegistrationsCount="totalRegistrationsCount"
                :events="events"
              />
            </div>
          </div>
          <div v-else-if="$page.props.auth.user.role == 'attendee'">
            <div class="p-6 text-gray-900 dark:text-gray-100"></div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
