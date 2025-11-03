<template>
  <auth-layout>
    <v-card width="400">
      <v-card-title class="text-center my-4">Join Us</v-card-title>
      <v-card-text>
        <v-form v-model="valid" @submit.prevent="handleSubmit">
          <v-text-field
            v-model="form.name"
            label="Name"
            variant="outlined"
            class="mb-2"
            :error-messages="'name' in form.errors ? [form.errors.name] : undefined "
          ></v-text-field>
          <v-text-field
            v-model="form.email"
            label="E-mail"
            variant="outlined"
            class="mb-2"
            :error-messages="'email' in form.errors ? [form.errors.email] : undefined "
          ></v-text-field>
          <v-text-field
            v-model="form.password"
            label="Password"
            type="password"
            variant="outlined"
            class="mb-2"
            :error-messages="'password' in form.errors ? [form.errors.password] : undefined "
          ></v-text-field>
          <v-text-field
            v-model="form.password_confirmation"
            label="Type Password Again"
            type="password"
            variant="outlined"
          ></v-text-field>

          <v-alert v-if="errors.activity" density="compact" type="error">{{ errors.activity }}</v-alert>

          <div class="mt-6 text-center">
            <v-btn type="submit" variant="elevated" color="primary" block :loading="loading">Submit</v-btn>
            <div class="mt-4">
              <p>Already have an account?</p>
              <Link href="/auth/login">login</Link>
            </div>
          </div>
        </v-form>
      </v-card-text>
    </v-card>
  </auth-layout>
</template>

<script setup>
import { ref, defineProps } from "vue"
import { Link, useForm } from "@inertiajs/vue3"

defineProps({
  errors: {
    type: Object,
    default: () => null
  }
})

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const valid = ref(false)
const loading = ref(false)

const handleSubmit = async () => {
  loading.value = true

  form.post('/auth/register', {
    preserveScroll: true,
    onSuccess: () => {
      console.log('success')
    },
    onFinish() {
      form.reset(['password', 'password_confirmation']);
      loading.value = false
    }
  });
}
</script>
