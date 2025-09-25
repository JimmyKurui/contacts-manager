<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import Create from './Create.vue';
import Edit from './Edit.vue';
import GroupContactModal from '@/Components/GroupContactModal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import api from '@/api.js';

const props = defineProps({
  contacts: {
    type: Object,
    required: true,
    default: () => ({ data: [], meta: {} }),
  },
  groups: {
    type: Object,
    required: true,
    default: () => ({ data: [], meta: {} }),
  },
});

// =========================== START PAGINATION =========================
const paginationMeta = computed(() => props.contacts.meta || {});

const perPageOptions = [10, 15, 25, 50, 100];
const selectedPerPage = ref(paginationMeta.per_page || 5);

function goToPage(page) {
  if (page && page !== paginationMeta.value.current_page) {
    router.get(route('contacts.index'), { page }, { preserveState: true, preserveScroll: true });
  }
}
const getPageLinks = computed(() => {
  const meta = paginationMeta.value;
  if (!meta.last_page) return [];
  const current = meta.current_page;
  const last = meta.last_page;
  let start = Math.max(1, current - 2);
  let end = Math.min(last, current + 2);
  if (end - start < 4) {
    if (start === 1) end = Math.min(last, start + 4);
    if (end === last) start = Math.max(1, end - 4);
  }
  const pages = [];
  for (let i = start; i <= end; i++) pages.push(i);
  return pages;
});
// ================== END PAGINATION ==================

// ================== START MODALS ==================
const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedContact = ref(null);
const showConfirmModal = ref(false);
const contactToDelete = ref(null);

const showGroupModal = ref(false);
const groupModalContact = ref(null);
const groupModalContactGroups = ref([]);

const openCreateModal = () => {
  showEditModal.value = false;
  selectedContact.value = null;
  showCreateModal.value = true;
};

const onCreateModalClose = () => {
  showCreateModal.value = false;
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

const handleDeleteCancelled = () => {
  showConfirmModal.value = false;
  contactToDelete.value = null;
};

const openGroupModal = async (contact) => {
  groupModalContact.value = contact;
  groupModalContactGroups.value = contact.groups || [];
  showGroupModal.value = true;
};

const onGroupModalClose = () => {
  showGroupModal.value = false;
  groupModalContact.value = null;
  groupModalContactGroups.value = [];
};
// ===================== END MODALS =====================

const attachContactToGroup = async (groupId, contactId) => {
  try {
    const res = await api.post(route('groups.attachContact', groupId), { contactId });
    return res.data;
  } catch (e) {
    console.log('Failed to attach contact to group', e)
  }
}; 

const addGroup = async(data) => {
  try {
    const res = await api.post(route('groups.store'), data);
    return res.data.group;
  } catch(e) {
    console.log('Failed to add group', e);
  }
}

const handleAddGroup = async ({groupName, exists}) => {
  if (!groupModalContact.value) return;
  try {
    let group = {}
    if (!exists) {
      const data = { name: groupName };
      group = await addGroup(data);
      props.groups.data.some(g => g.id === group.id) || props.groups.data.push(group);
    } else {
      group = props.groups.data.find(g => g.name.toLowerCase() === groupName.toLowerCase());
    }
    await attachContactToGroup(group.id, groupModalContact.value.id);
    groupModalContactGroups.value.some(g => g.id === group.id) || groupModalContactGroups.value.push(group);
  } catch (e) {
    console.log('Failed to handle group add', e)
  }
};

const handleDetachGroup = async (groupId) => {
  if (!groupModalContact.value) return;
  try {
    await api.post(route('groups.detachContact', groupId), {contactId: groupModalContact.value.id});
    groupModalContactGroups.value = groupModalContactGroups.value.filter(g => g.id != groupId);
    groupModalContact.value.groups = groupModalContact.value.groups.filter(g => g.id !== groupId);
  } catch (e) {
    console.log('failed to detach group', e);
  }
};
// ================== END MODALS ==================

const refetchContacts = () => {
  router.reload({ only: ['contacts']});
};

const handleDeleteConfirmed = async () => {
  if (!contactToDelete.value) return;
  try {
    await api.delete(route('contacts.destroy', contactToDelete.value.id));
    // alert component for successs
    refetchContacts();
  } catch (error) {
    // *alert component for error
    console.log('Failed to delete contact.');
  } finally {
    showConfirmModal.value = false;
    contactToDelete.value = null;
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

    <Create :show="showCreateModal" @close="onCreateModalClose" @created="refetchContacts" />

    <div class="p-6 bg-white rounded-lg shadow overflow-x-auto">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
          <PrimaryButton @click="openCreateModal">Add Contact</PrimaryButton>
          <PrimaryButton>Bulk Delete</PrimaryButton>
        </div>
        <div>
          <SecondaryButton>Import</SecondaryButton>
        </div>
      </div>
        <div class="flex items-center justify-between mb-2">
          <div class="flex items-center gap-2">
            <label for="perPage" class="text-sm font-medium">Records per page:</label>
            <select id="perPage" v-model="selectedPerPage" class="border rounded px-2 py-1 text-sm">
              <option v-for="option in perPageOptions" :key="option" :value="option">{{ option }}</option>
            </select>
          </div>
          <div class="text-sm text-gray-500">
            Showing {{ props.contacts.meta?.from || 0 }} - {{ props.contacts.meta?.to || 0 }} of {{ props.contacts.meta?.total || 0 }} contacts
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
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Groups</th>
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
            <td class="px-6 py-4 whitespace-nowrap">
              <span v-if="contact.groups && contact.groups.length">
                <span v-for="group in contact.groups" :key="group.id"
                  class="inline-block px-2 py-1 mr-1 mb-1 rounded-full text-xs font-semibold"
                  :style="`background-color: ${group.color || '#e9ecef'}; color: #333; border: 1px solid #ccc;`"
                >{{ group.name }}</span>
              </span>
              <span v-else class="text-gray-400 text-xs">No groups</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ contact.created_at }}</td>
            <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 flex flex-wrap gap-1">
              <Link :href="route('contacts.show', contact.id)"
                class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600 mr-1">View</Link>
              <button class="px-2 py-1 bg-indigo-500 text-white rounded hover:bg-indigo-600 mr-1"
                @click="() => openGroupModal(contact)">Group</button>
              <button class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 mr-1"
                @click="openEditModal(contact)">Edit</button>
              <button class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600"
                @click="confirmDelete(contact)">Delete</button>
            </td>
          </tr>
          <tr v-if="!contacts.data.length">
            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No contacts found.</td>
          </tr>
        </tbody>
      </table>
      <!-- Pagination controls -->
      <div class="flex justify-center items-center gap-1 mt-4">
        <button
          class="px-2 py-1 rounded border text-xs"
          :disabled="paginationMeta.current_page === 1"
          @click="goToPage(1)"
        >First</button>
        <button
          class="px-2 py-1 rounded border text-xs"
          :disabled="paginationMeta.current_page === 1"
          @click="goToPage(paginationMeta.current_page - 1)"
        >Previous</button>
        <button
          v-for="page in getPageLinks"
          :key="page"
          class="px-2 py-1 rounded border text-xs"
          :class="{ 'bg-blue-600 text-white': page === paginationMeta.current_page }"
          @click="goToPage(page)"
        >{{ page }}</button>
        <button
          class="px-2 py-1 rounded border text-xs"
          :disabled="paginationMeta.current_page === paginationMeta.last_page"
          @click="goToPage(paginationMeta.current_page + 1)"
        >Next</button>
        <button
          class="px-2 py-1 rounded border text-xs"
          :disabled="paginationMeta.current_page === paginationMeta.last_page"
          @click="goToPage(paginationMeta.last_page)"
        >Last</button>
      </div>
      <Edit :show="showEditModal" :contact="selectedContact" @close="onEditModalClose" @updated="refetchContacts" />
      <ConfirmModal
        :show="showConfirmModal"
        :title="'Delete Contact'"
        :message="`Are you sure you want to delete ${contactToDelete?.value?.first_name || ''} ${contactToDelete?.value?.last_name || ''}? This action cannot be undone.`"
        :confirmText="'Delete'"
        :cancelText="'Cancel'"
        @confirm="handleDeleteConfirmed"
        @cancel="handleDeleteCancelled"
      />
      <GroupContactModal
        :show="showGroupModal"
        :contact="groupModalContact"
        :groups="groups"
        :contactGroups="groupModalContactGroups"
        @close="onGroupModalClose"
        @add-group="handleAddGroup"
        @detach-group="handleDetachGroup"
      />
    </div>
  </AuthenticatedLayout>
</template>