<template>
  <profile-layout>
    <template #title>General Info</template>
      <div class="text-subtitle-1">
        Name:
        <span class="text-caption ml-2">{{ user.name }}</span>
      </div>
    <div class="text-subtitle-1">
      Email:
      <span class="text-caption ml-2">{{ user.email }}</span>
    </div>
  </profile-layout>
</template>

<script setup>
import { ref, defineProps } from 'vue';
import { router } from '@inertiajs/vue3';

defineProps({
  user: {
    type: Object,
    default: () => null
  }
})
</script>