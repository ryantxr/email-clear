<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
const props = defineProps<{ connected: boolean }>()
const form = useForm({})
</script>

<template>
    <Head title="Gmail Settings" />
    <h1 class="mb-4 text-xl font-bold">Gmail Connection</h1>
    <div v-if="props.connected" class="space-y-4">
        <p>Your Gmail account is connected.</p>
        <form method="post" :action="route('settings.gmail.destroy')">
            <input type="hidden" name="_method" value="delete" />
            <button class="rounded bg-red-500 px-4 py-2 text-white">Disconnect</button>
        </form>
    </div>
    <div v-else class="space-y-4">
        <p>No Gmail account connected.</p>
        <form method="post" :action="route('settings.gmail.redirect')">
            <button class="rounded bg-blue-500 px-4 py-2 text-white">Connect Gmail</button>
        </form>
    </div>
</template>
