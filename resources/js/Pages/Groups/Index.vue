<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import CreateGroupModal from './Partials/CreateFormModal.vue';
import EditGroupModal from './Partials/EditFormModal.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import api from '@/api.js'

const props = defineProps({
  groups: {
    type: Object,
    required: true,
    default: () => ({ data: [] }),
  },
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedGroup = ref(null);
const showConfirmDeleteModal = ref(false);
const groupToDelete = ref(null);

const openCreateModal = () => {
  showCreateModal.value = true;
};
const onCreateModalClose = () => {
  showCreateModal.value = false;
};
const handleCreateGroup = async (data) => {
  try {
    const res = await api.post(route('groups.store'), data);
    props.groups.data.push(res.data.group);
    showCreateModal.value = false;
  } catch (e) {
    alert('Failed to create group');
  }
};

const openEditModal = (group) => {
  selectedGroup.value = group;
  showEditModal.value = true;
};
const onEditModalClose = () => {
  showEditModal.value = false;
  selectedGroup.value = null;
};
const handleUpdateGroup = async (data) => {
  try {
    const res = await api.put(route('groups.update', data.id), data);
    const idx = props.groups.data.findIndex(g => g.id === data.id);
    if (idx !== -1) props.groups.data[idx] = res.data.group;
    showEditModal.value = false;
    selectedGroup.value = null;
  } catch (e) {
    alert('Failed to update group');
  }
};
const handleDelete = (group) => {
  groupToDelete.value = group;
  showConfirmDeleteModal.value = true;
};
const onDeleteCancelled = () => {
  showConfirmDeleteModal.value = false;
  groupToDelete.value = null;
};
const onDeleteConfirmed = async () => {
  if (!groupToDelete.value) return;
  try {
    await api.delete(route('groups.destroy', groupToDelete.value.id));
    props.groups.data = props.groups.data.filter(g => g.id !== groupToDelete.value.id);
    showConfirmDeleteModal.value = false;
    groupToDelete.value = null;
  } catch (e) {
    console.log('Failed to delete group', e);
  }
};

const icons = [
  '📁', '🗂️', '👥', '📝', '⭐', '🔖', '📦', '🧩', '🛡️', '🎯', '💡', '🔗', '🧑‍🤝‍🧑', '🧑‍💻', '🧑‍🏫', '🧑‍🔬'
];
function getRandomIcon(groupId) {
  return icons[groupId % icons.length];
}
</script>

<template>
  <Head title="Groups" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Groups</h2>
    </template>
    <div class="p-6 bg-white rounded-lg shadow">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
          <PrimaryButton @click="openCreateModal">Add Group</PrimaryButton>
          <PrimaryButton>Bulk Assign Contacts</PrimaryButton>
        </div>
        <div>
          <SecondaryButton>Import</SecondaryButton>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-1">
        <div
          v-for="group in groups.data"
          :key="group.id"
          class="rounded-lg shadow border flex flex-col  aspect-w-9 aspect-h-16 min-h-[250px]"
        >
          <!-- Header -->
          <div
            :style="{ backgroundColor: group.color || '#e9ecef', height: '40%' }"
            class="flex flex-col items-center justify-center rounded-t-lg py-2"
          >
          <span class="text-2xl mt-1">{{ getRandomIcon(group.id) }}</span>
            <span class="text-lg font-bold text-white text-center leading-tight">{{ group.name }}</span>
          </div>
          <!-- Body -->
          <div class="flex-1 px-3 py-2 flex flex-col justify-between py-4" style="height: 40%;">
            <div
                class="text-sm text-gray-700 mb-1 text-center overflow-hidden"
                :title="group.description"
            >
                {{
                    group.description && group.description.length > 80
                        ? group.description.slice(0, 77) + '...'
                        : (group.description || 'No description')
                }}
            </div>
            <div class="flex items-center justify-center text-xs text-gray-500 mt-3">
              <span><strong>Contacts: <span class="font-semibold">{{ group.contacts.length ?? 0 }}</span></strong></span>
            </div>
        </div>
            <div class="flex gap-2 justify-center mt-1">
                <SecondaryButton @click="openEditModal(group)">Edit</SecondaryButton>
                <SecondaryButton @click="handleDelete(group)">Delete</SecondaryButton>
            </div>
        </div>
      </div>

      <CreateGroupModal
        :show="showCreateModal"
        @close="onCreateModalClose"
        @create="handleCreateGroup"
      />
      <EditGroupModal
        :show="showEditModal"
        :group="selectedGroup"
        @close="onEditModalClose"
        @update="handleUpdateGroup"
      />
      <ConfirmModal
        :show="showConfirmDeleteModal"
        :title="'Delete Group'"
        :message="`Are you sure you want to delete ${groupToDelete?.value?.name || ''}? This action cannot be undone.`"
        :confirmText="'Delete'"
        :cancelText="'Cancel'"
        @confirm="onDeleteConfirmed"
        @cancel="onDeleteCancelled"
      />
    </div>
  </AuthenticatedLayout>
</template>
