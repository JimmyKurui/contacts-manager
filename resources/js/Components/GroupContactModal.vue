<script setup>
import { ref, computed } from 'vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    show: Boolean,
    contact: Object,
    groups: Object, 
    contactGroups: Object,
});
const emit = defineEmits(['close', 'add-group', 'detach-group']);

const input = ref('');
const filteredGroups = computed(() => {
    if (!input.value) return props.groups.data;
    return props.groups.data.filter(g => g.name.toLowerCase().includes(input.value.toLowerCase()));
});
const inputMatches = computed(() => filteredGroups.value.length > 0);

const addGroup = () => {
    if (!input.value) return;
    emit('add-group', {groupName: input.value, exists: inputMatches.value});
    input.value = '';
};
const detachGroup = (groupId) => {
    emit('detach-group', groupId);
};
</script>

<template>
    <Modal :show="show" @close="$emit('close')" maxWidth="lg">
        <div class="flex flex-col sm:flex-row gap-6 p-6">
            <div class="flex-1">
                <label class="block mb-2 font-semibold">Add to Group</label>
                <div class="relative">
                    <input
                        v-model="input"
                        list="groups-list"
                        class="w-full border rounded px-3 py-2"
                        placeholder="Type to search or add group"
                        @keydown.enter.prevent="addGroup"
                    />
                    <datalist id="groups-list">
                        <option v-for="g in filteredGroups" :key="g.id" :value="g.name" />
                    </datalist>
                    <button
                        v-if="input && !inputMatches"
                        class="absolute right-2 top-2 text-green-600"
                        @click="addGroup"
                        title="Add group"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </button>
                </div>
            </div>
            <div class="flex-1">
                <label class="block mb-2 font-semibold">Current Groups</label>
                <ul class="space-y-2">
                    <li v-for="g in contactGroups" :key="g.id" class="flex items-center justify-between bg-gray-100 rounded px-3 py-2">
                        <span>{{ g.name }}</span>
                        <button @click="detachGroup(g.id)" class="text-red-500 hover:text-red-700" title="Remove">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </li>
                    <li v-if="!contactGroups.length" class="text-gray-400">No groups assigned.</li>
                </ul>
            </div>
        </div>
    </Modal>
</template>
