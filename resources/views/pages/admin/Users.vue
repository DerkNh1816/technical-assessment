<template>
  <admin-layout>
    <template #breadcrumb>
      <v-breadcrumbs :items="['Admin', 'Users']"></v-breadcrumbs>
    </template>

    <div class="d-flex justify-end mb-2">
      <v-btn @click="openCreate">Create</v-btn>
    </div>
    <v-dialog
      v-model="dialog"
      max-width="500px"
    >
      <v-card>
        <v-card-title>
          <span class="text-h5">Edit user</span>
        </v-card-title>

        <v-card-text>
          <v-text-field
            v-model="editForm.name"
            label="Name"
            variant="outlined"
          ></v-text-field>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            variant="text"
            @click="closeEdit"
          >
            Cancel
          </v-btn>
          <v-btn
            color="primary"
            variant="elevated"
            :loading="saving"
            @click="handleSubmitEdit"
          >
            Save
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="createDialog"
      max-width="500px"
      persistent
    >
      <v-card>
        <v-card-title>
          <span class="text-h5">Create a new user</span>
        </v-card-title>

        <v-card-text>
          <v-text-field
            v-model="createForm.name"
            label="Name"
            variant="outlined"
            :error-messages="'name' in createForm.errors ? [createForm.errors.name] : undefined"
          ></v-text-field>
          <v-text-field
            v-model="createForm.email"
            label="Email"
            variant="outlined"
            :error-messages="'email' in createForm.errors ? [createForm.errors.email] : undefined"
          ></v-text-field>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            variant="text"
            @click="closeCreate"
          >
            Cancel
          </v-btn>
          <v-btn
            color="primary"
            variant="elevated"
            :loading="adding"
            @click="handleSubmitCreate"
          >
            Save
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-card>
      <v-data-table
        :headers="headers"
        :items="users"
        height="400"
        item-value="name"
      >
        <template v-slot:item.is_admin="{ value }">
          <v-chip label>
            {{ value ? 'Admin' : 'User' }}
          </v-chip>
        </template>
        <template v-slot:item.status="{ item }">
          <v-chip label :color="item.status === 'pending' ? 'red' : 'green'" class="text-capitalize">
            {{ item.status }}
          </v-chip>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-btn @click="handleStatusChange(item)" :loading="item.loading" :disabled="item.is_admin">
            {{ item.status === 'approved' ? 'Suspend' : 'Approve' }}
          </v-btn>
          <v-btn class="ml-1" :disabled="item.is_admin" @click="openEdit(item)">Edit</v-btn>
        </template>
      </v-data-table>
    </v-card>
  </admin-layout>
</template>

<script setup>
import { defineProps, ref, toRef } from 'vue';
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  users: {
    type: Array,
    default: () => []
  }
})
const form = useForm({
  status: ''
});
const editForm = useForm({
  id: null,
  name: ''
})
const createForm = useForm({
  name: '',
  email: ''
})

const dialog = ref(false)
const createDialog = ref(false)
const saving = ref(false)
const adding = ref(false)
const headers = ref([
  { title: 'Name', key: 'name' },
  { title: 'Email', key: 'email' },
  { title: 'Role', key: 'is_admin' },
  { title: 'Status', key: 'status' },
  { title: 'Actions', key: 'actions' }
])

const handleStatusChange = (item) => {
  item.loading = true

  form.status = item.status === 'approved' ? 'pending' : 'approved'
  form.put(`/admin/users/${item.id}`, {
    preserveScroll: true,
    onFinish() {
      item.loading = false
    }
  });
}
const openEdit = (item) => {
  editForm.name = item.name
  editForm.id = item.id

  dialog.value = true
}
const openCreate = () => {
  createForm.reset()

  createDialog.value = true
}
const closeEdit = () => {
  dialog.value = false
}
const closeCreate = () => {
  createDialog.value = false
}
const handleSubmitEdit = () => {
  saving.value = true

  editForm.put(`/admin/users/${editForm.id}`, {
    preserveScroll: true,
    onSuccess() {
      dialog.value = false
    },
    onFinish() {
      saving.value = false
    }
  });
}
const handleSubmitCreate = () => {
  adding.value = true

  createForm.post('/admin/users', {
    preserveScroll: true,
    onSuccess() {
      createForm.reset()
      createDialog.value = false
    },
    onFinish() {
      adding.value = false
    }
  });
}
</script>