<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';

interface PlanInfo {
    id: number;
    name: string;
    description: string | null;
    price: number | null;
    features: { data?: string[] } | string[];
}

interface Props {
    plans: PlanInfo[];
    currentPlan: string;
}

const props = defineProps<Props>();
const form = useForm({});

const upgrade = () => {
    router.get(route('billing.checkout'));
};

const cancel = () => {
    form.post(route('billing.cancel'));
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Billing', href: '/billing' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Billing" />
        <div class="mx-auto max-w-6xl space-y-8 p-4">
            <h1 class="text-center text-3xl font-bold">Choose your plan</h1>
            <p class="text-center text-gray-400">Current plan: {{ props.currentPlan }}</p>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div
                    v-for="plan in props.plans"
                    :key="plan.id"
                    :class="['relative flex flex-col items-center space-y-6 rounded-lg bg-black h-96 xw-96', plan.name === 'Pro' ? 'border border-blue-700' : 'border border-gray-600']"
                >
                    <div class="text-4xl font-bold">{{ plan.name }}</div>
                    <div class="text-2xl font-bold text-gray-100">
                        <template v-if="plan.price !== null">
                            ${{ plan.price }}<span v-if="plan.price > 0" class="text-sm font-normal">/month</span>
                        </template>
                        <template v-else>Contact us</template>
                    </div>
                    <div class="w-full space-y-1">
                        <div
                            v-for="feature in (plan.features.data ?? plan.features)"
                            :key="feature"
                            class="flex items-center ml-8"
                        >
                            <svg
                                class="mr-3 w-5 text-green-500"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <span class="text-gray-100">{{ feature }}</span>
                        </div>
                    </div>
                    <div class="absolute bottom-6 left-1/2 -translate-x-1/2">
                        <template v-if="plan.name.toLowerCase() === props.currentPlan">
                            <span class="text-sm text-gray-400">Current Plan</span>
                        </template>
                        <template v-else-if="plan.name === 'Pro' && props.currentPlan === 'free'">
                            <button class="cursor-pointer rounded-md border border-gray-700 bg-gradient-to-r from-blue-500 to-purple-700 px-3 py-2 text-white" @click="upgrade">
                                Upgrade
                            </button>
                        </template>
                        <template v-else-if="plan.name === 'Pro' && props.currentPlan === 'pro'">
                            <button class="cursor-pointer rounded-md border border-gray-700 bg-gray-600 px-3 py-2 text-white" @click="cancel">
                                Cancel
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
