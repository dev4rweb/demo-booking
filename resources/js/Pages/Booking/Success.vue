<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import LocaleSwitcher from '@/Components/LocaleSwitcher.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

const props = defineProps({
    business: Object,
    booking: Object,
});

const { t, locRoute } = useI18n();
const format = (iso) => new Date(iso).toLocaleString(undefined, { timeZone: props.booking.timezone });
</script>

<template>
    <Head :title="t('booking.success_title')" />
    <div class="min-h-screen">
        <header class="mx-auto flex max-w-xl items-center justify-between px-4 py-5">
            <Link :href="locRoute('welcome')" class="flex items-center gap-2">
                <ApplicationLogo class="h-9 w-9" />
                <span class="text-sm font-semibold">{{ business.name }}</span>
            </Link>
            <div class="flex gap-2"><ThemeToggle /><LocaleSwitcher /></div>
        </header>
        <main class="mx-auto max-w-xl px-4 pb-16">
            <div class="surface-card p-6 sm:p-8">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent">{{ t('booking.confirmed') }}</p>
                <h1 class="mt-2 text-3xl font-semibold">{{ t('booking.success_title') }}</h1>
                <p class="mt-3 text-sm leading-7 text-muted">{{ t('booking.success_body') }}</p>
                <dl class="mt-6 space-y-2 text-sm">
                    <div class="flex justify-between gap-4"><dt class="text-muted">{{ t('booking.step_service') }}</dt><dd>{{ booking.service }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-muted">{{ t('booking.step_staff') }}</dt><dd>{{ booking.staff }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-muted">{{ t('booking.step_slot') }}</dt><dd>{{ format(booking.starts_at) }}</dd></div>
                </dl>
                <a :href="booking.manage_url" class="mt-6 inline-flex rounded-xl bg-accent px-4 py-2.5 text-sm font-semibold text-accent-fg">{{ t('booking.manage') }}</a>
            </div>
        </main>
    </div>
</template>
