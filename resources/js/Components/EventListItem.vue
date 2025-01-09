<script setup>
defineProps({
    event:Object
})
</script>
<template>
   <div class="card bg-base-100 w-96 shadow-xl">
  <figure>
    <img
      :src="event.image"
      alt="image" />
  </figure>
  <div class="card-body">
    <h2 class="card-title">{{event.name}}</h2>
    <p>{{event.description}}</p>
    <div class="card-actions justify-end">
      <button class="btn btn-primary">Show</button>
    </div>
  </div>
</div>
</template>

