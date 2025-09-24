<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    contact: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head :title="contact.first_name + ' ' + contact.last_name" />
    <AuthenticatedLayout>
        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left column: Avatar & Bio -->
                <div class="bg-gray-900 rounded-lg p-8 flex flex-col items-center text-white">
                    <img
                        :src="contact.avatar_path || 'https://ui-avatars.com/api/?name=' + contact.first_name + '+' + contact.last_name"
                        alt="Avatar"
                        class="w-32 h-32 rounded-full mb-4 object-cover border-4 border-gray-700"
                    />
                    <h2 class="text-2xl font-bold mb-2">{{ contact.first_name }} {{ contact.last_name }}</h2>
                    <p class="text-gray-300 mb-2">{{ contact.job_title || 'No job title' }}</p>
                    <p class="text-gray-400 mb-2">{{ contact.company || 'No company' }}</p>
                    <p class="text-gray-400 mb-2">{{ contact.email }}</p>
                    <p class="text-gray-400 mb-2">{{ contact.mobile_phone || contact.work_phone }}</p>
                    <div class="mt-4">
                        <span v-if="contact.is_favorite" class="inline-block px-3 py-1 bg-yellow-500 text-black rounded-full text-xs">Favorite</span>
                    </div>
                </div>
                <!-- Right column: Other details -->
                <div class="bg-white rounded-lg p-8 shadow">
                    <h3 class="text-lg font-semibold mb-4">Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="font-medium">Address:</span>
                            <span class="ml-2 text-gray-700">{{ contact.address || 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="font-medium">City:</span>
                            <span class="ml-2 text-gray-700">{{ contact.city || 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="font-medium">State:</span>
                            <span class="ml-2 text-gray-700">{{ contact.state || 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="font-medium">Country:</span>
                            <span class="ml-2 text-gray-700">{{ contact.country || 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="font-medium">Postal Code:</span>
                            <span class="ml-2 text-gray-700">{{ contact.postal_code || 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="font-medium">Date of Birth:</span>
                            <span class="ml-2 text-gray-700">{{ contact.date_of_birth || 'N/A' }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="font-medium">Notes:</span>
                            <span class="ml-2 text-gray-700">{{ contact.notes || 'None' }}</span>
                        </div>
                    </div>
                    <div class="mt-8 flex gap-2">
                        <Link :href="route('contacts.edit', contact.id)" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Edit</Link>
                        <Link :href="route('contacts.index')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded">Back</Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
