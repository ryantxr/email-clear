<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { loadStripe, Stripe, StripeElements } from '@stripe/stripe-js';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

interface Plan {
    id: number;
    name: string;
    price: number | null;
}

interface Props {
    plan: Plan;
    publishableKey: string;
}

const props = defineProps<Props>();
let stripe: Stripe | null = null;
let elements: StripeElements | null = null;
const paymentElementMounted = ref(false);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Billing', href: '/billing' },
    { title: 'Checkout', href: '/billing/checkout' },
];

onMounted(async () => {
    stripe = await loadStripe(props.publishableKey);
    if (!stripe) return;
    const resp = await fetch(route('billing.intent'), { method: 'POST' });
    const { clientSecret } = await resp.json();
    elements = stripe.elements({ clientSecret });
    const payment = elements.create('payment');
    payment.mount('#payment-element');
    paymentElementMounted.value = true;
});

const pay = async () => {
    if (!stripe || !elements) return;
    await stripe.confirmPayment({
        elements,
        confirmParams: { return_url: route('billing.success') },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Checkout" />
        <div class="mx-auto max-w-lg space-y-6 p-4">
            <h1 class="text-center text-3xl font-bold">
                Upgrade to {{ props.plan.name }}
            </h1>
            <p class="text-center text-gray-400">
                ${{ props.plan.price }}/month
            </p>
            <div id="payment-element" v-show="paymentElementMounted"></div>
            <button
                class="cursor-pointer mt-4 w-full rounded bg-blue-600 py-2 text-white"
                @click="pay"
            >
                Pay
            </button>
        </div>
    </AppLayout>
</template>
