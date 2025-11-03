<template>
    <auth-layout>
        <v-card width="400">
            <v-card-title class="text-center my-4">Welcome Back</v-card-title>
            <v-card-text>
                <v-form v-model="valid" @submit.prevent="handleSubmit">
                    <v-text-field
                        v-model="form.email"
                        label="E-mail"
                        variant="outlined"
                    ></v-text-field>
                    <v-text-field
                        v-model="form.password"
                        label="Password"
                        type="password"
                        variant="outlined"
                        :error-messages="
                            'password' in form.errors
                                ? [form.errors.password]
                                : undefined
                        "
                        class="mt-2"
                    ></v-text-field>

                    <v-alert
                        v-if="errors.activity"
                        density="compact"
                        type="error"
                        >{{ errors.activity }}</v-alert
                    >

                    <div class="mt-6 text-center">
                        <v-btn
                            type="submit"
                            variant="elevated"
                            color="primary"
                            block
                            :loading="loading"
                            >Submit</v-btn
                        >
                        <div class="mt-4">
                            <p>Don't have an account yet?</p>
                            <Link href="/auth/register">Create an account</Link>
                        </div>
                    </div>
                </v-form>
            </v-card-text>
        </v-card>
    </auth-layout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'

defineProps({
    errors: {
        type: Object,
        default: () => null,
    },
})

const loading = ref(false)
const form = useForm({
    email: '',
    password: '',
})

const valid = ref(false)

const handleSubmit = async () => {
    loading.value = true

    form.post('/auth/login', {
        preserveScroll: true,
        onSuccess: () => {
            console.log('success')
        },
        onFinish() {
            form.reset(['email', 'password'])
            loading.value = false
        },
    })
}
</script>
