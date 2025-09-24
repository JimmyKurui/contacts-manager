<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import Create from './Create.vue';
import Edit from './Edit.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import api from '@/api.js';

const props = defineProps({
  contacts: {
    type: Array,
    required: true,
    default: () => [],
  }
});

const refetchContacts = () => {
  router.reload({ only: ['contacts']});
};

const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedContact = ref(null);
const showConfirmModal = ref(false);
const contactToDelete = ref(null);

const onCreateModalClose = () => {
  showCreateModal.value = false;
};

const openCreateModal = () => {
  showEditModal.value = false;
  selectedContact.value = null;
  showCreateModal.value = true;
};

const openEditModal = (contact) => {
  showCreateModal.value = false;
  selectedContact.value = contact;
  showEditModal.value = true;
};

const onEditModalClose = () => {
  showEditModal.value = false;
  selectedContact.value = null;
};

const confirmDelete = (contact) => {
  contactToDelete.value = contact;
  showConfirmModal.value = true;
};

const handleDeleteConfirmed = async () => {
  if (!contactToDelete.value) return;
  try {
    await api.delete(`/contacts/${contactToDelete.value.id}`);
    refetchContacts();
  } catch (error) {
    alert('Failed to delete contact.');
  } finally {
    showConfirmModal.value = false;
    contactToDelete.value = null;
  }
};

const handleDeleteCancelled = () => {
  showConfirmModal.value = false;
  contactToDelete.value = null;
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
          <Create :show="showCreateModal" @close="onCreateModalClose" @created="refetchContacts" />
          <Edit :show="showEditModal" :contact="selectedContact" @close="onEditModalClose" @updated="refetchContacts" />
          <PrimaryButton @click="openCreateModal">Add Contact</PrimaryButton>
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
              <button class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 mr-1"
                @click="openEditModal(contact)">Edit</button>
              <button class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600"
                @click="confirmDelete(contact)">Delete</button>
              <ConfirmModal
                :show="showConfirmModal"
                :title="'Delete Contact'"
                :message="`Are you sure you want to delete ${contactToDelete?.value?.first_name || ''} ${contactToDelete?.value?.last_name || ''}? This action cannot be undone.`"
                :confirmText="'Delete'"
                :cancelText="'Cancel'"
                @confirm="handleDeleteConfirmed"
                @cancel="handleDeleteCancelled"
              />
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