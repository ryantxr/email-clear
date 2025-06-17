<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import HeadingSmall from '@/components/HeadingSmall.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';

interface Token {
    id: number;
    email: string;
}

interface Props {
    tokens: Token[];
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Gmail accounts',
        href: '/settings/gmail',
    },
];

const form = useForm({});

const disconnect = (id: number) => {
    form.delete(route('gmail.destroy', id));
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Gmail accounts" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Connected Gmail addresses" />

                <ul class="space-y-2">
                    <li v-for="token in props.tokens" :key="token.id" class="flex justify-between items-center">
                        <span>{{ token.email }}</span>
                        <Button variant="destructive" @click="disconnect(token.id)">
                            Disconnect
                        </Button>
                    </li>
                </ul>

                <div>
                    <Button as-child>
                        <a :href="route('gmail.connect')">Connect new Gmail</a>
                    </Button>
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
