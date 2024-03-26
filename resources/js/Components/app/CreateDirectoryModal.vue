<script setup>
import Modal from "@/Components/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import { useForm } from "@inertiajs/vue3";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { nextTick, ref } from "vue";

const form = useForm({
    name: '',
});

const directoryNameInput = ref(null);

const {modelValue} = defineProps({
    modelValue: Boolean,
});

const emit = defineEmits(['update:modelValue']);

function onShow() {
    nextTick(() => {
        directoryNameInput.value.focus();
    });
}

function createDirectory() {
    form.post(route('directory.create'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            form.reset();
            //show success message
        },
        onError: () => { directoryNameInput.value.focus(); },
    });
}

function closeModal() {
    emit('update:modelValue');
    form.clearErrors();
    form.reset();
}

</script>

<template>
    <modal :show="modelValue" @show="onShow" max-width="sm">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Create New Folder</h2>
            <div class="mt-6">
                <InputLabel for="directoryName" value="Folder Name"/>
                <TextInput
                    type="text"
                    id="directoryName"
                    ref="directoryNameInput"
                    v-model="form.name"
                    class="mt-1 block w-full"
                    :class="form.errors.name ?
                        'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                    placeholder="Folder Name"
                    @keyup.enter="createDirectory"
                />
                <InputError :message="form.errors.name" class="mt-2"/>
            </div>
            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                <PrimaryButton @click="createDirectory"
                    :disable="form.processing"
                    class="ml-2" :class="{ 'opacity-25': form.processing }">
                    Create
                </PrimaryButton>
            </div>
        </div>
    </modal>
</template>

<style scoped>

</style>
