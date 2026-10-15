<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

defineProps({ clients: Object });
const { t } = useI18n();
</script>

<template>
    <Head :title="t('clients.title')" />
    <AuthenticatedLayout>
        <template #header><h2 class="text-xl font-semibold">{{ t('clients.title') }}</h2></template>
        <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6">
            <div class="hidden overflow-hidden rounded-2xl border border-line md:block">
                <table class="min-w-full text-sm">
                    <thead class="bg-surface-2 text-left text-xs uppercase text-muted">
                        <tr>
                            <th class="px-4 py-3">{{ t('common.name') }}</th>
                            <th class="px-4 py-3">{{ t('common.phone') }}</th>
                            <th class="px-4 py-3">{{ t('common.email') }}</th>
                            <th class="px-4 py-3">{{ t('clients.visits') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in clients.data" :key="row.id" class="border-t border-line">
                            <td class="px-4 py-3">{{ row.name }}</td>
                            <td class="px-4 py-3">{{ row.phone }}</td>
                            <td class="px-4 py-3">{{ row.email }}</td>
                            <td class="px-4 py-3">{{ row.bookings_count }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="grid gap-3 md:hidden">
                <article v-for="row in clients.data" :key="row.id" class="surface-card p-4">
                    <p class="font-medium">{{ row.name }}</p>
                    <p class="text-sm text-muted">{{ row.phone }}</p>
                    <p class="text-sm text-muted">{{ row.email }}</p>
                    <p class="mt-1 text-xs uppercase tracking-wide text-accent">{{ row.bookings_count }} {{ t('clients.visits') }}</p>
                </article>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
