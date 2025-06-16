<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import HeadingSmall from '@/components/HeadingSmall.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { type BreadcrumbItem } from '@/types';

interface Account {
    id: number;
    host: string;
    username: string;
}

interface Props {
    accounts: Account[];
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'IMAP accounts', href: '/settings/imap' },
];

const form = useForm({
    host: '',
    port: 993,
    encryption: 'ssl',
    username: '',
    password: '',
});

const submit = () => {
    form.post(route('imap.store'));
};

const remove = (id: number) => {
    form.delete(route('imap.destroy', id));
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="IMAP accounts" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Existing IMAP accounts" />

                <ul class="space-y-2">
                    <li v-for="acc in props.accounts" :key="acc.id" class="flex justify-between items-center">
                        <span>{{ acc.username }}@{{ acc.host }}</span>
                        <Button variant="destructive" @click="remove(acc.id)">Delete</Button>
                    </li>
                </ul>

                <HeadingSmall title="Add account" />

                <form class="space-y-4" @submit.prevent="submit">
                    <div class="flex space-x-2">
                        <Input v-model="form.host" placeholder="Host" class="flex-1" />
                        <Input v-model.number="form.port" placeholder="Port" class="w-24" />
                    </div>
                    <Input v-model="form.encryption" placeholder="Encryption (ssl/tls/none)" />
                    <Input v-model="form.username" placeholder="Username" />
                    <Input v-model="form.password" type="password" placeholder="Password" />
                    <Button type="submit" :disabled="form.processing">Save</Button>
                </form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
