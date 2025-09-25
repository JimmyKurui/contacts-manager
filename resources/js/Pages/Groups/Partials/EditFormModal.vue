<script setup>
import { ref, watch } from 'vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  show: Boolean,
  group: Object,
});
const emit = defineEmits(['close', 'update']);

const name = ref('');
const description = ref('');
const color = ref('#3b82f6');

watch(() => props.group, (g) => {
  if (g) {
    name.value = g.name || '';
    description.value = g.description || '';
    color.value = g.color || '#3b82f6';
  }
}, { immediate: true });

const handleUpdate = () => {
  emit('update', { id: props.group.id, name: name.value, description: description.value, color: color.value });
};
</script>
<template>
  <Modal :show="show" @close="$emit('close')" maxWidth="sm">
    <div class="p-6">
      <h3 class="text-lg font-bold mb-4">Edit Group</h3>
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
        <button class="px-3 py-1 bg-blue-600 text-white rounded" @click="handleUpdate">Update</button>
      </div>
    </div>
  </Modal>
</template>
