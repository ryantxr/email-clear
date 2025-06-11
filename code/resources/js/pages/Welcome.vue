<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a]">
        <header class="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl">
            <nav class="flex items-center justify-end gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="route('login')"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                    >
                        Log in
                    </Link>
                    <Link
                        :href="route('register')"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </header>
        <div class="flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0">
            <main class="flex w-full max-w-[335px] flex-col gap-8 overflow-hidden rounded-lg lg:max-w-4xl">
                <section
                    class="rounded-lg bg-white p-6 pb-12 text-[13px] leading-[20px] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] lg:p-20 dark:bg-[#161615] dark:text-[#EDEDEC] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
                >
                    <h1 class="mb-1 font-medium">Email overload is real</h1>
                    <p class="mb-2 text-[#706f6c] dark:text-[#A1A09A]">
                        Unwanted emails clutter your inbox, create distractions and waste time.
                    </p>
                    <p class="mb-4 text-[#706f6c] dark:text-[#A1A09A]">
                        Email Clear labels solicitation messages so you can keep your inbox tidy.
                    </p>
                </section>

                <section
                    class="rounded-lg bg-white p-6 pb-12 text-[13px] leading-[20px] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] lg:p-20 dark:bg-[#161615] dark:text-[#EDEDEC] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
                >
                    <h2 class="mb-1 font-medium">How it works</h2>
                    <p class="text-[#706f6c] dark:text-[#A1A09A]">
                        Connect your Gmail account once and we'll scan for solicitation messages. Every unwanted email is labeled automatically so you can review them when it's convenient.
                    </p>
                </section>

                <section
                    class="rounded-lg bg-white p-6 pb-12 text-[13px] leading-[20px] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] lg:p-20 dark:bg-[#161615] dark:text-[#EDEDEC] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
                >
                    <h2 class="mb-4 font-medium">Pricing</h2>
                    <div class="grid gap-4 text-center sm:grid-cols-3">
                        <div class="rounded-md border p-4">
                            <h3 class="font-semibold">Free</h3>
                        </div>
                        <div class="rounded-md border p-4">
                            <h3 class="font-semibold">Pro</h3>
                            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">$9 / month</p>
                        </div>
                        <div class="rounded-md border p-4">
                            <h3 class="font-semibold">Super</h3>
                            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">$19 / month</p>
                        </div>
                    </div>
                </section>
            </main>
        </div>
        <div class="hidden h-14.5 lg:block"></div>
    </div>
</template>
