<script setup>
import { MenuItem } from "@headlessui/vue";
import { emitter, FILE_UPLOAD_STARTED } from "@/event-bus.js";

function onChange(event) {
    emitter.emit(FILE_UPLOAD_STARTED, event.target.files);
}

</script>

<template>
    <MenuItem v-slot="{ active }">
        <a href="#" @click="$refs.fileUpload.click()" class="text-gray-700 block px-4 py-2 text-sm">
            Upload Files

            <input type="file" @change="onChange" ref="fileUpload" hidden multiple/>
        </a>
    </MenuItem>
</template>

<style scoped>

</style>
