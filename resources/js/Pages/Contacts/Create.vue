<script setup>
import Modal from '@/Components/Modal.vue';
import { computed, ref } from 'vue';
import api from '@/api';

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
});
const emit = defineEmits(['close', 'created']);

const showModal = computed(() => props.show);
const loading = ref(false);
const errors = ref({});
const success = ref('');

const contact = ref({
  first_name: '',
  last_name: '',
  email: '',
  work_phone: '',
  address: '',
  company: '',
  job_title: '',
});

const closeModal = () => {
  loading.value = false;
  errors.value = {};
  success.value = '';
  contact.value = {
    first_name: '',
    last_name: '',
    email: '',
    work_phone: '',
    address: '',
    company: '',
    job_title: '',
  };
  emit('close');
};

const validate = () => {
  const errs = {};
  if (!contact.value.first_name) errs.first_name = 'First name is required.';
  if (!contact.value.last_name) errs.last_name = 'Last name is required.';
  if (!contact.value.email) errs.email = 'Email is required.';
  else if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(contact.value.email)) errs.email = 'Email is invalid.';
  return errs;
};

const submit = async () => {
  errors.value = validate();
  if (Object.keys(errors.value).length) return;
  loading.value = true;
  try {
    await api.post(route('contacts.store'), contact.value);
    success.value = 'Contact created successfully!';
    setTimeout(() => {
      closeModal();
      emit('created');
    }, 1000);
  } catch (err) {
    if (err.response && err.response.data && err.response.data.errors) {
      errors.value = err.response.data.errors;
    } else {
      errors.value = { general: 'Failed to create contact.' };
    }
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <Modal :show="showModal" @close="closeModal" maxWidth="lg">
    <div class="p-6">
      <h3 class="text-lg font-semibold mb-4">Create Contact</h3>
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1">First Name</label>
          <input v-model="contact.first_name" type="text" class="w-full border rounded px-3 py-2" />
          <span v-if="errors.first_name" class="text-red-500 text-xs">{{ errors.first_name }}</span>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Last Name</label>
          <input v-model="contact.last_name" type="text" class="w-full border rounded px-3 py-2" />
          <span v-if="errors.last_name" class="text-red-500 text-xs">{{ errors.last_name }}</span>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Email</label>
          <input v-model="contact.email" type="email" class="w-full border rounded px-3 py-2" />
          <span v-if="errors.email" class="text-red-500 text-xs">{{ errors.email }}</span>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Work Phone</label>
          <input v-model="contact.work_phone" type="text" class="w-full border rounded px-3 py-2" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Address</label>
          <input v-model="contact.address" type="text" class="w-full border rounded px-3 py-2" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Company</label>
          <input v-model="contact.company" type="text" class="w-full border rounded px-3 py-2" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Job Title</label>
          <input v-model="contact.job_title" type="text" class="w-full border rounded px-3 py-2" />
        </div>
        <div v-if="errors.general" class="text-red-500 text-xs">{{ errors.general }}</div>
        <div v-if="success" class="text-green-500 text-xs">{{ success }}</div>
        <div class="flex justify-end mt-4">
          <button type="button" class="px-4 py-2 bg-gray-300 text-gray-700 rounded mr-2" @click="closeModal">Cancel</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded" :disabled="loading">
            <span v-if="loading">Creating...</span>
            <span v-else>Create</span>
          </button>
        </div>
      </form>
    </div>
  </Modal>
</template>
