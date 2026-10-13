<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

defineProps({
    stats: Object,
    upcoming: Array,
    publicUrl: String,
});

const { t } = useI18n();
const money = (cents) => `$${(cents / 100).toFixed(2)}`;
const format = (iso) => new Date(iso).toLocaleString([], { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
</script>

<template>
    <Head :title="t('dashboard.title')" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold tracking-tight">{{ t('dashboard.title') }}</h2>
        </template>
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="surface-card p-5">
                    <p class="text-xs uppercase tracking-wide text-muted">{{ t('dashboard.week_bookings') }}</p>
                    <p class="mt-2 text-3xl font-semibold">{{ stats.week_bookings }}</p>
                </div>
                <div class="surface-card p-5">
                    <p class="text-xs uppercase tracking-wide text-muted">{{ t('dashboard.revenue') }}</p>
                    <p class="mt-2 text-3xl font-semibold">{{ money(stats.revenue_cents) }}</p>
                </div>
                <div class="surface-card p-5">
                    <p class="text-xs uppercase tracking-wide text-muted">{{ t('dashboard.no_shows') }}</p>
                    <p class="mt-2 text-3xl font-semibold">{{ stats.no_shows }}</p>
                </div>
                <div class="surface-card p-5">
                    <p class="text-xs uppercase tracking-wide text-muted">{{ t('dashboard.plan') }}</p>
                    <p class="mt-2 text-3xl font-semibold capitalize">{{ stats.plan }}</p>
                </div>
            </div>

            <a :href="publicUrl" class="mt-6 inline-flex text-sm text-accent underline-offset-2 hover:underline">{{ t('dashboard.public_page') }}</a>

            <section class="mt-8">
                <h3 class="text-lg font-semibold">{{ t('dashboard.upcoming') }}</h3>
                <div class="mt-4 space-y-3">
                    <article v-for="item in upcoming" :key="item.id" class="surface-card flex flex-col gap-1 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-medium">{{ item.client }} · {{ item.service }}</p>
                            <p class="text-sm text-muted">{{ item.staff }} · {{ format(item.starts_at) }}</p>
                        </div>
                    </article>
                    <p v-if="!upcoming.length" class="text-sm text-muted">{{ t('dashboard.empty') }}</p>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
