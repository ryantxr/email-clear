<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Head, useForm } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';

interface Props {
    frontPageText: string | null;
    pricing: string | null;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin',
        href: '/admin/content',
    },
];

const form = useForm({
    front_page_text: props.frontPageText || '',
    pricing: props.pricing || '',
});

const submit = () => {
    form.post(route('admin.content.update'));
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Site Content" />
        <div class="space-y-6 p-4">
            <HeadingSmall title="Site Content" />
            <form @submit.prevent="submit" class="space-y-4">
                <div class="grid gap-2">
                    <label for="front_page_text">Front page text</label>
                    <textarea id="front_page_text" class="w-full border" rows="3" v-model="form.front_page_text" />
                </div>
                <div class="grid gap-2">
                    <label for="pricing">Pricing</label>
                    <textarea id="pricing" class="w-full border" rows="3" v-model="form.pricing" />
                </div>
                <Button type="submit">Save</Button>
            </form>
        </div>
    </AppLayout>
</template>
