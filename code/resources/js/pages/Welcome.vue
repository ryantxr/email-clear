<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Brain, Zap, Shield, ChartColumn, Clock, Lock } from 'lucide-vue-next';

interface PlanInfo {
    id: number;
    name: string;
    description: string | null;
    price: number | null;
    features: { data?: string[] } | string[];
}

interface Props {
    frontPageText: string | null;
    pricing: string | null;
    plans: PlanInfo[];
}

const props = defineProps<Props>();
</script>

<template>

    <Head title="Welcome" />
    <div class="flex min-h-screen flex-col bg-gray-900 text-gray-100">
        <header class="border-b border-gray-600 bg-black">
            <nav class="mx-auto flex max-w-6xl items-center justify-between p-4">
                <div class="flex items-center space-x-2">
                    <svg class="w-8" width="100%" height="100%" viewBox="0 0 75 59" version="1.1"
                        xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                        xml:space="preserve" xmlns:serif="http://www.serif.com/"
                        style="fill-rule:evenodd;clip-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:1.5;">
                        <g>
                            <g>
                                <path d="M22.751,6.098l2.999,5.034"
                                    style="fill:none;stroke:#161616;stroke-width:2.89px;" />
                                <path d="M33.878,3.387l-0,7.197"
                                    style="fill:none;stroke:#161616;stroke-width:2.89px;" />
                                <path d="M45.098,5.931l-3.572,5.432"
                                    style="fill:none;stroke:#161616;stroke-width:2.89px;" />
                            </g>
                            <g>
                                <path
                                    d="M59.601,18.713c-0.112,-1.783 -1.651,-3.14 -3.435,-3.027l-49.207,3.102c-1.783,0.113 -3.14,1.652 -3.027,3.435l1.895,30.064c0.113,1.783 1.652,3.139 3.435,3.027l49.208,-3.103c1.783,-0.112 3.139,-1.651 3.027,-3.435l-1.896,-30.063Z"
                                    style="fill:#735ef9;stroke:#ffcab1;stroke-width:2.89px;" />
                                <path d="M20.535,33.449l-14.926,-13.83"
                                    style="fill:none;stroke:#ffcab1;stroke-width:2.89px;" />
                                <path d="M21.357,39.168l-14.234,14.899"
                                    style="fill:none;stroke:#ffcab1;stroke-width:2.89px;" />
                                <path d="M45.202,32.018l13.283,-15.457"
                                    style="fill:none;stroke:#ffcab1;stroke-width:2.89px;" />
                            </g>
                            <g>
                                <path style="fill:#dadada;"
                                    d="M24.87,24.248c1.778,-0.223 3.484,1.695 3.807,4.278c0.324,2.584 -0.858,4.863 -2.636,5.085c-1.778,0.223 -3.484,-1.695 -3.807,-4.278c-0.324,-2.584 0.858,-4.863 2.636,-5.085Z" />
                                <ellipse cx="26.215" cy="28.217" rx="0.74" ry="1.032" style="fill:#161616;" />
                            </g>
                            <g>
                                <ellipse cx="40.131" cy="28.006" rx="3.138" ry="4.291" style="fill:#dadada;" />
                                <ellipse cx="40.727" cy="27.164" rx="0.698" ry="0.809" style="fill:#161616;" />
                            </g>
                            <path d="M27.715,37.541c1.696,3.898 10.464,5.082 12.367,-1.255"
                                style="fill:none;stroke:#dadada;stroke-width:3.05px;" />
                            <path
                                d="M52.702,44.609l-4.102,-5.176c-2.542,-2.977 -7.345,-0.027 -4.891,4.207l7.198,9.24c1.719,1.665 3.769,1.357 5.761,-0.895l13.776,-17.588c1.004,-1.508 0.799,-4.044 -0.541,-4.865c-1.289,-0.79 -2.627,-0.764 -4.022,0.319l-13.179,14.758Z"
                                style="fill:#4d9a5b;stroke:#dadada;stroke-width:2.89px;" />
                        </g>
                    </svg>
                    <span class="text-xl">Email Clear</span>
                </div>
                <div class="flex items-center space-x-4">
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="text-sm hover:text-gray-300">
                    Dashboard</Link>
                    <template v-else>
                        <Link :href="route('login')" class="text-sm hover:text-gray-300">Sign in</Link>
                        <Link :href="route('register')"
                            class="rounded bg-gradient-to-r from-blue-500 to-purple-700 px-4 py-2 text-sm text-white">
                        Get Started</Link>
                    </template>
                </div>
            </nav>
        </header>

        <main class="flex flex-col items-center px-4 py-12 space-y-16">
            <section class="text-center space-y-6">
                <span class="inline-block rounded-full border border-blue-700 px-4 py-2 text-blue-200">Stop wasting time
                    on
                    unwanted emails</span>
                <h1 class="text-5xl font-bold">
                    Your inbox,
                    <span class="bg-gradient-to-r from-blue-500 to-purple-700 bg-clip-text text-transparent">finally
                        clean</span>
                </h1>
                <p class="mx-auto max-w-2xl text-xl text-gray-400">Email Clear automatically identifies and deletes
                    solicitation
                    emails using advanced AI, so you can focus on what matters most.</p>
                <div class="flex justify-center space-x-4">
                    <Link :href="route('register')"
                        class="rounded bg-gradient-to-r from-blue-500 to-purple-700 px-6 py-3 text-white">Start free
                    trial
                    </Link>
<!-- 
                    <Link href="#" class="flex items-center space-x-2 text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-play-circle">
                        <circle cx="12" cy="12" r="10" />
                        <polygon points="10 8 16 12 10 16 10 8" />
                    </svg>
                    <span>Watch demo</span>
                    </Link>
                     -->
                </div>
            </section>

            <section class="flex justify-center">
                <img src="/images/eclear/img1.png" alt="App screenshot" class="rounded shadow-lg" />
            </section>

            <section class="text-center space-y-4">
                <h2 class="text-3xl font-bold">Powerful features for a <span
                        class="bg-gradient-to-r from-blue-500 to-purple-700 bg-clip-text text-transparent">cleaner
                        inbox</span>
                </h2>
                <p class="text-lg text-gray-400">Email Clear uses cutting-edge technology to keep your email organized
                    and your
                    productivity high.</p>
            </section>

            <section class="grid max-w-5xl grid-cols-1 gap-6 md:grid-cols-3 text-gray-400">
                <div class="flex flex-col items-center space-y-4 rounded-lg border border-gray-700 bg-black p-6">
                    <div class="rounded-xl bg-gradient-to-r from-blue-500 to-purple-700 p-4">
                        <Brain class="text-white" />
                    </div>
                    <div class="text-xl font-bold text-white">AI-Powered Detection</div>
                    <div class="text-sm">Advanced machine learning algorithms identify solicitation emails with 99.7%
                        accuracy.
                    </div>
                </div>
                <div class="flex flex-col items-center space-y-4 rounded-lg border border-gray-700 bg-black p-6">
                    <div class="rounded-xl bg-gradient-to-r from-blue-500 to-purple-700 p-4">
                        <Zap class="text-white" />
                    </div>
                    <div class="text-xl font-bold text-white">Instant Processing</div>
                    <div class="text-sm">Real-time email analysis and deletion happens in milliseconds, not minutes.
                    </div>
                </div>
                <div class="flex flex-col items-center space-y-4 rounded-lg border border-gray-700 bg-black p-6">
                    <div class="rounded-xl bg-gradient-to-r from-blue-500 to-purple-700 p-4">
                        <Shield class="text-white" />
                    </div>
                    <div class="text-xl font-bold text-white">Safe &amp; Secure</div>
                    <div class="text-sm">Your emails are processed securely with enterprise-grade encryption and
                        privacy.</div>
                </div>
                <div class="flex flex-col items-center space-y-4 rounded-lg border border-gray-700 bg-black p-6">
                    <div class="rounded-xl bg-gradient-to-r from-blue-500 to-purple-700 p-4">
                        <ChartColumn class="text-white" />
                    </div>
                    <div class="text-xl font-bold text-white">Analytics Dashboard</div>
                    <div class="text-sm">Track how much time and mental energy you're saving with detailed insights.
                    </div>
                </div>
                <div class="flex flex-col items-center space-y-4 rounded-lg border border-gray-700 bg-black p-6">
                    <div class="rounded-xl bg-gradient-to-r from-blue-500 to-purple-700 p-4">
                        <Clock class="text-white" />
                    </div>
                    <div class="text-xl font-bold text-white">Save Hours Weekly</div>
                    <div class="text-sm">Reclaim 3-5 hours per week by eliminating email management overhead.</div>
                </div>
                <div class="flex flex-col items-center space-y-4 rounded-lg border border-gray-700 bg-black p-6">
                    <div class="rounded-xl bg-gradient-to-r from-blue-500 to-purple-700 p-4">
                        <Lock class="text-white" />
                    </div>
                    <div class="text-xl font-bold text-white">Privacy First</div>
                    <div class="text-sm">We never read your personal emails. Only solicitation patterns are analyzed.
                    </div>
                </div>
            </section>

            <section class="text-center space-y-4">
                <h2 class="text-5xl font-bold">Simple, transparent <span
                        class="bg-gradient-to-r from-blue-500 to-purple-700 bg-clip-text text-transparent">pricing</span>
                </h2>
                <p class="text-xl text-gray-400">Choose the plan that fits your needs. All plans include our core
                    AI-powered
                    email cleaning features.</p>

        </section>
        <section class="mb-8 text-center text-gray-400 ">
            <div class="mx-auto grid max-w-5xl grid-cols-3 gap-6">
                <div v-for="plan in props.plans" :key="plan.id"
                    :class="['relative flex flex-col items-center py-6 space-y-6 bg-black rounded-lg h-96 xw-96', plan.name === 'Pro' ? 'border border-blue-700' : 'border border-gray-600']">
                    <div class="text-4xl font-bold">
                        {{ plan.name }}
                    </div>
                    <div class="text-2xl font-bold text-gray-100">
                        ${{ plan.price }}<span v-if="plan.price && plan.price > 0" class="text-sm font-regular">/month</span>
                    </div>
                    <div class="w-full">
                        <div v-for="feature in (plan.features.data ?? plan.features)" :key="feature" class="flex items-center ml-8">
                            <span class="inline-block mr-3">
                                <svg class="w-6 text-green-500 size-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                            </span>
                            <span class="inline-block">{{ feature }}</span>
                        </div>
                    </div>
                    <div class="absolute bottom-6 left-1/2 -translate-x-1/2">
                        <button
                            :class="plan.name === 'Pro' ? 'px-3 py-2 text-white border border-gray-700 rounded-md bg-gradient-to-r from-blue-500 to-purple-700' : 'px-3 py-2 text-black bg-gray-300 border border-gray-700 rounded-md'">
                            Choose Plan
                        </button>
                    </div>
                </div>
                <div class="relative flex flex-col items-center py-6 space-y-6 bg-black rounded-lg h-96 xw-96 border border-gray-600">
                    <div class="text-4xl font-bold">
                        Enterprise
                    </div>
                    <div class="text-2xl font-bold text-gray-100">
                        Contact us
                    </div>
                    <div class="w-full">   
                        <div class="flex items-center ml-8">
                            <span class="inline-block mr-3">
                                <svg class="w-6 text-green-500 size-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                            </span>
                            <span class="inline-block">More email addresses</span>
                        </div>
                        <div class="flex items-center ml-8">
                            <span class="inline-block mr-3">
                                <svg class="w-6 text-green-500 size-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                            </span>
                            <span class="inline-block">More messages</span>
                        </div>
                        <div class="flex items-center ml-8">
                            <span class="inline-block mr-3">
                                <svg class="w-6 text-green-500 size-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                            </span>
                            <span class="inline-block">Webhooks</span>
                        </div>
                        <div class="flex items-center ml-8">
                            <span class="inline-block mr-3">
                                <svg class="w-6 text-green-500 size-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                            </span>
                            <span class="inline-block">Analytics</span>
                        </div>
                        <div class="flex items-center ml-8">
                            <span class="inline-block mr-3">
                                <svg class="w-6 text-green-500 size-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                            </span>
                            <span class="inline-block">More controls</span>
                        </div>
                    </div>
                    <div class="absolute bottom-6 left-1/2 -translate-x-1/2">
                        <button
                            class="px-3 py-2 text-white border border-gray-700 rounded-md bg-gradient-to-r from-blue-500 to-purple-700 px-3 py-2 text-black bg-gray-300 border border-gray-700 rounded-md">
                            Contact us
                        </button>
                    </div>
                </div>
            </div>
        </section>
            <section class="text-center space-y-4">
                <h2 class="text-5xl font-bold">Frequently asked <span
                        class="bg-gradient-to-r from-blue-500 to-purple-700 bg-clip-text text-transparent">questions</span>
                </h2>
                <p class="text-xl text-gray-400">Everything you need to know about Email Clear and how it works.</p>
                <div class="mt-6 text-gray-400">FAQ</div>
            </section>
        </main>

        <footer class="mt-16 bg-black px-5 py-6 text-center text-gray-400">
            <div class="mx-auto grid max-w-5xl grid-cols-1 gap-6 md:grid-cols-4">
                <div class="col-span-2 text-left">
                    <div class="mb-4 flex items-center space-x-2 text-gray-200">
                        <svg class="w-8" width="100%" height="100%" viewBox="0 0 75 59" version="1.1"
                            xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                            xml:space="preserve" xmlns:serif="http://www.serif.com/"
                            style="fill-rule:evenodd;clip-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:1.5;">
                            <g>
                                <g>
                                    <path d="M22.751,6.098l2.999,5.034"
                                        style="fill:none;stroke:#161616;stroke-width:2.89px;" />
                                    <path d="M33.878,3.387l-0,7.197"
                                        style="fill:none;stroke:#161616;stroke-width:2.89px;" />
                                    <path d="M45.098,5.931l-3.572,5.432"
                                        style="fill:none;stroke:#161616;stroke-width:2.89px;" />
                                </g>
                                <g>
                                    <path
                                        d="M59.601,18.713c-0.112,-1.783 -1.651,-3.14 -3.435,-3.027l-49.207,3.102c-1.783,0.113 -3.14,1.652 -3.027,3.435l1.895,30.064c0.113,1.783 1.652,3.139 3.435,3.027l49.208,-3.103c1.783,-0.112 3.139,-1.651 3.027,-3.435l-1.896,-30.063Z"
                                        style="fill:#735ef9;stroke:#ffcab1;stroke-width:2.89px;" />
                                    <path d="M20.535,33.449l-14.926,-13.83"
                                        style="fill:none;stroke:#ffcab1;stroke-width:2.89px;" />
                                    <path d="M21.357,39.168l-14.234,14.899"
                                        style="fill:none;stroke:#ffcab1;stroke-width:2.89px;" />
                                    <path d="M45.202,32.018l13.283,-15.457"
                                        style="fill:none;stroke:#ffcab1;stroke-width:2.89px;" />
                                </g>
                                <g>
                                    <path style="fill:#dadada;"
                                        d="M24.87,24.248c1.778,-0.223 3.484,1.695 3.807,4.278c0.324,2.584 -0.858,4.863 -2.636,5.085c-1.778,0.223 -3.484,-1.695 -3.807,-4.278c-0.324,-2.584 0.858,-4.863 2.636,-5.085Z" />
                                    <ellipse cx="26.215" cy="28.217" rx="0.74" ry="1.032" style="fill:#161616;" />
                                </g>
                                <g>
                                    <ellipse cx="40.131" cy="28.006" rx="3.138" ry="4.291" style="fill:#dadada;" />
                                    <ellipse cx="40.727" cy="27.164" rx="0.698" ry="0.809" style="fill:#161616;" />
                                </g>
                                <path d="M27.715,37.541c1.696,3.898 10.464,5.082 12.367,-1.255"
                                    style="fill:none;stroke:#dadada;stroke-width:3.05px;" />
                                <path
                                    d="M52.702,44.609l-4.102,-5.176c-2.542,-2.977 -7.345,-0.027 -4.891,4.207l7.198,9.24c1.719,1.665 3.769,1.357 5.761,-0.895l13.776,-17.588c1.004,-1.508 0.799,-4.044 -0.541,-4.865c-1.289,-0.79 -2.627,-0.764 -4.022,0.319l-13.179,14.758Z"
                                    style="fill:#4d9a5b;stroke:#dadada;stroke-width:2.89px;" />
                            </g>
                        </svg>
                        <span class="text-xl">Email Clear</span>
                    </div>
                    <p class="text-gray-400">Stop wasting time on unwanted emails. Email Clear automatically identifies
                        and
                        deletes solicitation emails, keeping your inbox clean and focused.</p>
                </div>
                <div class="text-left">
                    <div class="mb-2 font-bold text-white">Product</div>
                    <ul class="space-y-1">
                        <li>Features</li>
                        <li>Pricing</li>
                        <li>Integrations</li>
                    </ul>
                </div>
                <div class="text-left">
                    <div class="mb-2 font-bold text-white">Support</div>
                    <ul class="space-y-1">
                        <li>FAQ</li>
                        <li>Help Center</li>
                        <li>Contact</li>
                        <li>Status</li>
                    </ul>
                </div>
            </div>
        </footer>
    </div>
    <!-- class="h-96 h-128"-->
</template>
