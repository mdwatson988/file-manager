<script setup>
import Navigation from "@/Components/app/Navigation.vue";
import SearchForm from "@/Components/app/SearchForm.vue";
import UserSettingsDropdown from "@/Components/app/UserSettingsDropdown.vue";
import { onMounted, ref } from "vue";
import { emitter, FILE_UPLOAD_STARTED } from "@/event-bus.js";
import { useForm, usePage } from "@inertiajs/vue3";

const fileUploadForm = useForm({
    files: [],
    parent_id: null,
    relative_paths: null,
});
const page = usePage();

const dragOver = ref(false);

onMounted(() => {
    emitter.on(FILE_UPLOAD_STARTED, uploadFiles)
});

function onDragOver(event) {
    dragOver.value = true;
}

function onDragLeave(event) {
    dragOver.value = false;
}

function handleDrop(event) {
    dragOver.value = false;
    const files = event.dataTransfer.files;

    if (!files.length) {
        return;
    }

    uploadFiles(files);
}

function uploadFiles(files) {
    fileUploadForm.parent_id = page.props.directory.id;
    fileUploadForm.files = files;
    console.log(files);
    fileUploadForm.relative_paths = [...files].map(file => file.webkitRelativePath);

    fileUploadForm.post(route('file.store'), {
        // preserveScroll: true,
        // onSuccess: () => {
        //     fileUploadForm.reset();
        // },
    });
}
</script>

<template>
    <div class="h-screen bg-gray-50 flex w-full gap-4">
        <Navigation/>

        <main @drop.prevent="handleDrop"
            @dragover.prevent="onDragOver"
            @dragleave.prevent="onDragLeave"
            class="flex flex-col flex-1 px-4 overflow-hidden"
            :class="dragOver ? 'dropzone' : ''">

            <template v-if="dragOver" class="text-gray-500 text-center text-sm py-8">
                Drop files here to upload
            </template>
            <template v-else>
                <div class="flex items-center justify-between w-full">
                    <SearchForm/>
                    <UserSettingsDropdown/>
                </div>
                <div class="flex flex-1 flex-col overflow-hidden">
                    <slot/>
                </div>
            </template>
        </main>
    </div>
</template>

<style scoped>
.dropzone {
    width: 100%;
    height: 100%;
    color: #8d8d8d;
    border: 2px dashed gray;
    display: flex;
    justify-content: center;
    align-items: center;
}
</style>
