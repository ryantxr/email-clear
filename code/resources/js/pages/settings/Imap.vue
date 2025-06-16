<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';

interface Account {
    id: number;
    email: string;
}

interface Props {
    accounts: Account[];
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'IMAP accounts',
        href: '/settings/imap',
    },
];

const form = useForm({
    email: '',
    host: '',
    port: 993,
    encryption: 'ssl',
    username: '',
    password: '',
});

const submit = () => {
    form.post(route('imap.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset('password'),
    });
};

const remove = (id: number) => {
    useForm({}).delete(route('imap.destroy', id));
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="IMAP accounts" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Connected IMAP accounts" />
                <ul class="space-y-2">
                    <li v-for="account in props.accounts" :key="account.id" class="flex justify-between items-center">
                        <span>{{ account.email }}</span>
                        <Button variant="destructive" @click="remove(account.id)">
                            Remove
                        </Button>
                    </li>
                </ul>

                <HeadingSmall title="Add IMAP account" />
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input id="email" v-model="form.email" />
                        <InputError :message="form.errors.email" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="host">Host</Label>
                        <Input id="host" v-model="form.host" />
                        <InputError :message="form.errors.host" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="port">Port</Label>
                        <Input id="port" type="number" v-model="form.port" />
                        <InputError :message="form.errors.port" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="encryption">Encryption</Label>
                        <Input id="encryption" v-model="form.encryption" />
                        <InputError :message="form.errors.encryption" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="username">Username</Label>
                        <Input id="username" v-model="form.username" />
                        <InputError :message="form.errors.username" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="password">Password</Label>
                        <Input id="password" type="password" v-model="form.password" />
                        <InputError :message="form.errors.password" />
                    </div>
                    <Button :disabled="form.processing">Add account</Button>
                </form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
