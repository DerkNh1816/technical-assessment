<template>
  <profile-layout>
    <template #title>Reset Password</template>

    <v-form v-model="valid" @submit.prevent="handleSubmit">
      <v-text-field
        v-model="form.old_password"
        label="Original Password"
        variant="outlined"
        type="password"
        :error-messages="'old_password' in form.errors ? [form.errors.old_password] : undefined"
      ></v-text-field>
      <v-text-field
        v-model="form.password"
        label="New Password"
        type="password"
        variant="outlined"
        :error-messages="'password' in form.errors ? [form.errors.password] : undefined"
      ></v-text-field>

      <v-alert v-if="errors.message" density="compact" type="error" variant="tonal">{{ errors.message }}</v-alert>

      <div class="mt-6 text-center">
        <v-btn type="submit" variant="elevated" color="primary" block :loading="loading">Submit</v-btn>
      </div>
    </v-form>
  </profile-layout>
</template>

<script setup>
import { ref, defineProps } from 'vue';
import { Link, useForm } from "@inertiajs/vue3"

defineProps({
  errors: {
    type: Object,
    default: () => null
  }
})

const form = useForm({
  old_password: '',
  password: ''
});

const valid = ref(false)
const loading = ref(false)

const handleSubmit = async () => {
  loading.value = true

  form.post('/me/password-reset', {
    preserveScroll: true,
    onSuccess: () => {
      console.log('success')
    },
    onFinish() {
      form.reset(['password', 'old_password']);
      loading.value = false
    }
  });
}
</script>