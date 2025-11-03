// import './bootstrap';
import { createApp, h } from 'vue'
import { createInertiaApp } from "@inertiajs/vue3";

import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'
// import "../scss/app.scss";
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m.js';
import { Ziggy } from './ziggy';
import pinia from './store';

import AuthLayout from "@/views/layouts/Auth.vue";
import DefaultLayout from "@/views/layouts/Default.vue";
import AdminLayout from "@/views/layouts/Admin.vue";
import ProfileLayout from "@/views/layouts/Profile.vue";

createInertiaApp({
  resolve: (name) => {
    const pages = import.meta.glob("../views/pages/**/*.vue");

    return pages[`../views/pages/${name}.vue`]();
  },
  setup({ el, App, props, plugin }) {
    const vuetify = createVuetify({
      components,
      directives,
      defaults: {
        global: {
          ripple: false
        },
        VBtn: {
          // style: 'text-transform: none;'
        }
      }
    })

    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue, Ziggy)
      .use(vuetify)
      .use(pinia)
      .mixin({
        components: {
          AuthLayout,
          DefaultLayout,
          AdminLayout,
          ProfileLayout
        }
      })
      .mount(el)
  },
})
