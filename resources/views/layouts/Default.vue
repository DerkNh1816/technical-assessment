<template>
    <v-layout class="rounded rounded-md">
        <v-app-bar>
            <v-container class="d-flex align-center">
                <div>
                    <v-btn href="/">Laravel-Vuetify3-Inertia</v-btn>
                </div>

                <v-spacer></v-spacer>

                <v-menu transition="slide-y-transition">
                    <template v-slot:activator="{ props }">
                        <v-btn icon dark v-bind="props">
                            <v-icon>mdi-account-circle</v-icon>
                        </v-btn>
                    </template>

                    <v-list density="compact" nav>
                        <v-list-item
                            v-if="page.props.me.is_admin"
                            link
                            href="/admin/users"
                        >
                            <template v-slot:prepend>
                                <v-icon icon="mdi-account-tie"></v-icon>
                            </template>
                            <v-list-item-title>Admin</v-list-item-title>
                        </v-list-item>
                        <v-list-item
                            v-if="!page.props.me.is_admin"
                            link
                            href="/me/general"
                        >
                            <template v-slot:prepend>
                                <v-icon icon="mdi-account"></v-icon>
                            </template>
                            <v-list-item-title>Profile</v-list-item-title>
                        </v-list-item>
                        <v-list-item @click="logOut">
                            <template v-slot:prepend>
                                <v-icon icon="mdi-export"></v-icon>
                            </template>
                            <v-list-item-title>Logout</v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </v-container>
        </v-app-bar>

        <v-main class="">
            <v-container>
                <slot />
            </v-container>
        </v-main>

        <Chat />
    </v-layout>
</template>

<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3'
import Chat from '../../js/components/Chat.vue'

const form = useForm({})
const page = usePage({})

const logOut = () => {
    form.post('/auth/logout')
}
</script>
