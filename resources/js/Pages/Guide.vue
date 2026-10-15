<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import LocaleSwitcher from '@/Components/LocaleSwitcher.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

defineProps({
    bookingUrl: String,
});

const { t, locRoute } = useI18n();
</script>

<template>
    <Head :title="t('guide.title')" />

    <div class="min-h-screen">
        <header class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-5 sm:px-6">
            <Link :href="locRoute('welcome')" class="flex items-center gap-2">
                <ApplicationLogo class="h-9 w-9" />
                <span class="text-sm font-semibold tracking-tight">{{ t('app_name') }}</span>
            </Link>
            <div class="flex items-center gap-2">
                <ThemeToggle />
                <LocaleSwitcher />
            </div>
        </header>

        <main class="mx-auto max-w-4xl px-4 pb-16 sm:px-6">
            <h1 class="text-3xl font-semibold tracking-tight text-ink sm:text-4xl">{{ t('guide.title') }}</h1>
            <p class="mt-3 max-w-2xl text-muted">{{ t('guide.subtitle') }}</p>

            <div class="mt-8 grid gap-5 lg:grid-cols-2">
                <section class="surface-card p-5 sm:p-7">
                    <h2 class="text-xl font-semibold text-ink">{{ t('guide.client_title') }}</h2>
                    <p class="mt-3 text-sm leading-7 text-muted">{{ t('guide.client_intro') }}</p>
                    <ol class="mt-4 list-decimal space-y-3 ps-5 text-sm leading-6 text-ink">
                        <li>{{ t('guide.client_pick') }}</li>
                        <li>{{ t('guide.client_slot') }}</li>
                        <li>{{ t('guide.client_confirm') }}</li>
                        <li>{{ t('guide.client_manage') }}</li>
                    </ol>
                    <Link :href="bookingUrl" class="mt-6 inline-flex rounded-xl bg-accent px-4 py-2.5 text-sm font-semibold text-accent-fg">
                        {{ t('guide.open_booking') }}
                    </Link>
                </section>
                <section class="surface-card p-5 sm:p-7">
                    <h2 class="text-xl font-semibold text-ink">{{ t('guide.owner_title') }}</h2>
                    <ol class="mt-4 list-decimal space-y-3 ps-5 text-sm leading-6 text-ink">
                        <li>{{ t('guide.owner_login') }}</li>
                        <li>{{ t('guide.owner_calendar') }}</li>
                        <li>{{ t('guide.owner_crud') }}</li>
                        <li>{{ t('guide.owner_billing') }}</li>
                        <li>{{ t('guide.owner_telegram') }}</li>
                    </ol>
                    <Link :href="locRoute('login')" class="mt-6 inline-flex rounded-xl border border-line px-4 py-2.5 text-sm font-semibold text-ink transition hover:border-accent">
                        {{ t('guide.open_owner') }}
                    </Link>
                </section>
            </div>
        </main>
    </div>
</template>
