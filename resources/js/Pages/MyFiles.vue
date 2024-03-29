<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router, Link } from "@inertiajs/vue3";
import { HomeIcon, ChevronRightIcon } from "@heroicons/vue/20/solid/index.js";

const {files} = defineProps({
    files: Object,
    directory: Object,
    ancestors: Object,
})

function openDirectory(file) {
    if (!file.is_directory) {
        return;
    }

    router.visit(route('myFiles', { directory: file.path }))
}

</script>

<template>
    <AuthenticatedLayout>
        <nav class="flex items-center justify-between p-1 mb-3">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li v-for="ans of ancestors.data" :key="ans.id" class="inline-flex items-center">
                    <Link v-if="!ans.parent_id" :href="route('myFiles')"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        <HomeIcon class="w-5 h-5 mr-1" aria-hidden="true"/>
                        My Files
                    </Link>
                    <div v-else class="flex items-center">
                        <ChevronRightIcon class="w-6 h-6 text-gray-400" aria-hidden="true"/>
                        <Link :href="route('myFiles', { directory: ans.path })" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">{{ ans.name }}</Link>
                    </div>
                </li>
            </ol>
        </nav>
        <table v-if="files.data.length" class="min-w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                        Name
                    </th>
                    <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                        Owner
                    </th>
                    <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                        Last Modified
                    </th>
                    <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                        Size
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="file of files.data" :key="file.id"
                    @dblclick="openDirectory(file)"
                    class="bg-white border-b transition duration-300 ease-in-out hover:bg-gray-100 cursor-pointer">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ file.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ file.owner }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ file.updated_at }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ file.size }}
                    </td>
                </tr>
            </tbody>

        </table>
        <div v-if="!files.data.length" class="py-8 mt-4 text-center text-md text-gray-400">
            No files found
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
