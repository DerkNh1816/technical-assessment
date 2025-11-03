<template>
  <default-layout>
    <div class="">
      <div class="text-right mb-2">
        <v-btn color="primary" class="" @click="dialog = true">
          <v-icon>mdi-plus</v-icon>
          Create
        </v-btn>
      </div>
      <v-card>
        <v-table
          :items="tasks.data"
          hide-default-footer
          density="compact"
        >
          <thead>
            <tr>
              <th class="text-left">
                Title
              </th>
              <th class="text-left">
                Status
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in tasks.data"
              :key="item.id"
            >
              <td>{{ item.title }}</td>
              <td>{{ item.completed ? 'Completed' : 'Not completed' }}</td>
            </tr>
          </tbody>
        </v-table>
        <v-pagination
          v-model="tasks.current_page"
          :total="tasks.total"
          :length="tasks.total / tasks.per_page"
          density="compact"
          @update:model-value="handlePageChange"
        ></v-pagination>
      </v-card>
    </div>

    <v-dialog
      v-model="dialog"
      width="auto"
    >
      <v-card
        width="400"
        title="Create a new task."
      >
        <v-card-text>
          <v-form v-model="valid">
            <v-text-field
              v-model="form.title"
              variant="outlined"
              density="compact"
              label="Title"
            ></v-text-field>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-btn
            class="ms-auto"
            variant="elevated"
            color="primary"
            :loading="loading"
            @click="handleSubmit"
          >Ok</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </default-layout>
</template>

<script setup>
import { router, useForm } from '@inertiajs/vue3'

const props = defineProps({
  tasks: {
    type: Object
  }
})

const form = useForm({
  title: '',
});
const dialog = ref(false)
const valid = ref(false)
const loading = ref(false)

const handlePageChange = (v) => {
  router.get(props.tasks.path, {
    page: v
  })
}
const handleSubmit = async () => {
  loading.value = true

  form.post('/tasks', {
    preserveScroll: true,
    onFinish() {
      loading.value = false
      dialog.value = false
    }
  });
}
</script>