<script setup>
import { Link } from '@inertiajs/vue3'
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import CreateNewDropdown from "@/Components/app/CreateNewDropdown.vue";
import NavLink from "@/Components/NavLink.vue";

</script>

<template>
    <nav class="min-w-[200px]">
        <div>
            <Link class="h-[80px] px-3 flex items-center gap-3" :href="route('myFiles')">
                <ApplicationLogo
                    class="block h-9 w-auto fill-current text-gray-500"
                />
                LaraStorage
            </Link>
        </div>
        <div class="px-3">
            <CreateNewDropdown />
            <div class="py-3">
                <NavLink :href="route('myFiles')" :active="$page.url === '/my-files'">
                    My Files</NavLink>
                <NavLink href="">
                    Shared With Me</NavLink>
                <NavLink href="">
                    Shared By Me</NavLink>
                <NavLink href="" :active="false">
                    Trash</NavLink>
            </div>
        </div>
    </nav>
</template>

<style scoped>

</style>
