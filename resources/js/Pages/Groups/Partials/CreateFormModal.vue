<script setup>
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  show: Boolean,
});
const emit = defineEmits(['close', 'create']);

const name = ref('');
const description = ref('');
const color = ref('#3b82f6');

const handleCreate = () => {
  emit('create', { name: name.value, description: description.value, color: color.value });
  name.value = '';
  description.value = '';
  color.value = '#3b82f6';
};
</script>
<template>
  <Modal :show="show" @close="$emit('close')" maxWidth="sm">
    <div class="p-6">
      <h3 class="text-lg font-bold mb-4">Create Group</h3>
      <div class="mb-3">
        <label class="block text-sm font-medium mb-1">Name</label>
        <input v-model="name" class="w-full border rounded px-3 py-2" placeholder="Group name" />
      </div>
      <div class="mb-3">
        <label class="block text-sm font-medium mb-1">Description</label>
        <input v-model="description" class="w-full border rounded px-3 py-2" placeholder="Description" />
      </div>
      <div class="mb-3">
        <label class="block text-sm font-medium mb-1">Color</label>
        <input v-model="color" type="color" class="w-10 h-10 p-0 border rounded" />
      </div>
      <div class="flex gap-2 mt-4 justify-end">
        <button class="px-3 py-1 bg-gray-200 rounded" @click="$emit('close')">Cancel</button>
        <button class="px-3 py-1 bg-blue-600 text-white rounded" @click="handleCreate">Create</button>
      </div>
    </div>
  </Modal>
</template>
