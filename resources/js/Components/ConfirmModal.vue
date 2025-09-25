<script setup>
import Modal from '@/Components/Modal.vue';
import { ref } from 'vue';

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: 'Confirm',
  },
  message: {
    type: String,
    default: 'Are you sure?',
  },
  confirmText: {
    type: String,
    default: 'Confirm',
  },
  cancelText: {
    type: String,
    default: 'Cancel',
  },
});
const emit = defineEmits(['confirm', 'cancel']);

const loading = ref(false);

const handleConfirm = () => {
  loading.value = true;
  emit('confirm');
  loading.value = false;
};

const handleCancel = () => {
  emit('cancel');
};
</script>

<template>
  <Modal :show="props.show" @close="handleCancel" maxWidth="sm">
    <div class="p-6">
      <h3 class="text-lg font-semibold mb-4">{{ props.title }}</h3>
      <p class="mb-6 text-gray-700">{{ props.message }}</p>
      <div class="flex justify-end gap-2">
        <button type="button" class="px-4 py-2 bg-gray-300 text-gray-700 rounded" @click="handleCancel">{{ props.cancelText }}</button>
        <button type="button" class="px-4 py-2 bg-red-600 text-white rounded" @click="handleConfirm" :disabled="loading">
          <span v-if="loading">Processing...</span>
          <span v-else>{{ props.confirmText }}</span>
        </button>
      </div>
    </div>
  </Modal>
</template>
