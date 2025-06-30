<script setup>
defineProps({
  events: Array,
  eventCount: String,
  totalRevenue: String,
  totalRegistrationsCount: String,
});
</script>

<template>
  <div>
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3">
      <div class="stats shadow bg-primary">
        <div class="stat">
          <div class="stat-title text-white">Total Number of Events</div>
          <div class="stat-value text-white">{{ eventCount }}</div>
          <div class="stat-desc text-white">all time</div>
        </div>
      </div>
      <div class="stats shadow bg-success">
        <div class="stat">
          <div class="stat-title text-white">Total Revenue</div>
          <div class="stat-value text-white">{{ totalRevenue }}</div>
          <div class="stat-desc text-white">all time</div>
        </div>
      </div>
      <div class="stats shadow bg-warning">
        <div class="stat">
          <div class="stat-title text-white">Total Registration Count</div>
          <div class="stat-value text-white">{{ totalRegistrationsCount }}</div>
          <div class="stat-desc text-white">all time</div>
        </div>
      </div>
    </div>
    <div class="overflow-x-auto mt-4 card shadow border p-2">
      <div class="font-bold text-sm">Recent Events</div>
      <table class="table table-sm">
        <thead>
          <tr>
            <th></th>
            <th>Name</th>
            <th>Image</th>
            <th>Date</th>
            <!-- <th>Due Date</th>
          <th>Registration Expiration</th> -->
          </tr>
        </thead>
        <tbody v-for="(event, index) in events.data" :key="index">
          <tr>
            <th>{{ index + 1 }}</th>
            <th>{{ event.name }}</th>
            <th>
              <div class="h-12 w-12">
                <img :src="event.image" class="mask mask-squircle" />
              </div>
            </th>
            <th>{{ event.date }}</th>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
