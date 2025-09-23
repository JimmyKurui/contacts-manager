<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Create from './Create.vue';
import { Head, Link, ref } from '@inertiajs/vue3';
import api from '@/api.js';
import { ref } from 'vue';

const props = defineProps({
  contacts: {
    type: Array,
    required: true,
    default: () => [],
  }
});

const refetchContacts = () => {
  router.reload({ only: ['contacts'] });
};

const showCreateModal = ref(false);
const onCreateModalClose = (success) => {
  showCreateModal.value = false;
  if (success === true) {
    refetchContacts();
  }
};

const handleDelete = async (id) => {
  try {
    await api.delete(`/contacts/${id}`);
    refetchContacts();
  } catch (error) {
    alert('Failed to delete contact.');
  }
};

</script>


<template>
  <Head title="Contacts" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Contacts
      </h2>
    </template>

    <div class="p-6 bg-white rounded-lg shadow overflow-x-auto">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
          <Create :show="showCreateModal" @close="onCreateModalClose(success=true)" />
          <PrimaryButton @click="showCreateModal = true">Add Contact</PrimaryButton>
          <PrimaryButton>Bulk Delete</PrimaryButton>
        </div>
        <div>
          <SecondaryButton>Import</SecondaryButton>
        </div>
      </div>
      <table class="min-w-full divide-y divide-gray-200" style="min-width: 900px;">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Address</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Company</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Job Title</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created At</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="contact in contacts.data" :key="contact.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ contact.first_name + contact.last_name }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ contact.email }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ contact.work_phone }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ contact.address }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ contact.company }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ contact.job_title }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ contact.created_at }}</td>
            <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 flex flex-wrap gap-1">
              <Link :href="route('contacts.show', contact.id)"
                class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600 mr-1">View</Link>
              <Link :href="route('contacts.edit', contact.id)"
                class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 mr-1">Edit</Link>
              <button class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600"
                @click="handleDelete(contact.id)">Delete</button>
            </td>
          </tr>
          <tr v-if="!contacts.data.length">
            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No contacts found.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </AuthenticatedLayout>
</template>