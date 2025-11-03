<template>
    <v-layout class="rounded rounded-md">
      <v-app-bar>
        <v-container class="d-flex align-center">
          <Link href="/">Home</Link>
          
          <v-spacer></v-spacer>
          
          <v-menu
            transition="slide-y-transition"
          >
            <template v-slot:activator="{ props }">
              <v-btn
                icon
                dark
                v-bind="props"
              >
                <v-icon>mdi-account-circle</v-icon>
              </v-btn>
            </template>

            <v-list
              density="compact"
              nav
            >
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

      <v-navigation-drawer temporary v-model="drawer">
        <v-list
          density="compact"
          nav
        >
          <v-list-item prepend-icon="mdi-account-multiple" title="Users" href="/admin/users" />
        </v-list>
      </v-navigation-drawer>

      <v-main class="">
        <v-container>
          <div class="d-flex align-center">
            <v-btn density="comfortable" variant="text" icon="mdi-menu" @click="drawer = !drawer" />
            <slot name="breadcrumb"></slot>
          </div>
          <slot />
        </v-container>
      </v-main>
    </v-layout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3'

const drawer = ref(false)
const form = useForm({});

const logOut = () => {
  form.post('/auth/logout');
}
</script>