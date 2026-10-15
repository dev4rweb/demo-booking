<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

const props = defineProps({
    bookings: Object,
    filters: Object,
});

const { t, locRoute } = useI18n();
const format = (iso) => new Date(iso).toLocaleString([], { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
const money = (cents) => `$${(cents / 100).toFixed(2)}`;

const filter = (status) => router.get(locRoute('bookings.index'), { status }, { preserveState: true });
const update = (id, status) => router.patch(locRoute('bookings.status', { booking: id }), { status });
</script>

<template>
    <Head :title="t('bookings.title')" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-xl font-semibold">{{ t('bookings.title') }}</h2>
                <select :value="filters.status" class="rounded-xl border-line bg-surface text-sm" @change="filter($event.target.value)">
                    <option value="">{{ t('bookings.all') }}</option>
                    <option value="confirmed">{{ t('booking.status_confirmed') }}</option>
                    <option value="cancelled">{{ t('booking.status_cancelled') }}</option>
                    <option value="completed">{{ t('booking.status_completed') }}</option>
                    <option value="no_show">{{ t('booking.status_no_show') }}</option>
                </select>
            </div>
        </template>
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6">
            <div class="hidden overflow-hidden rounded-2xl border border-line md:block">
                <table class="min-w-full text-sm">
                    <thead class="bg-surface-2 text-left text-xs uppercase text-muted">
                        <tr>
                            <th class="px-4 py-3">{{ t('booking.step_slot') }}</th>
                            <th class="px-4 py-3">{{ t('common.name') }}</th>
                            <th class="px-4 py-3">{{ t('booking.step_service') }}</th>
                            <th class="px-4 py-3">{{ t('booking.step_staff') }}</th>
                            <th class="px-4 py-3">{{ t('common.status') }}</th>
                            <th class="px-4 py-3">{{ t('common.price') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in bookings.data" :key="row.id" class="border-t border-line">
                            <td class="px-4 py-3">{{ format(row.starts_at) }}</td>
                            <td class="px-4 py-3">{{ row.client }}<div class="text-xs text-muted">{{ row.phone }}</div></td>
                            <td class="px-4 py-3">{{ row.service }}</td>
                            <td class="px-4 py-3">{{ row.staff }}</td>
                            <td class="px-4 py-3">
                                <select :value="row.status" class="rounded-lg border-line bg-surface text-xs" @change="update(row.id, $event.target.value)">
                                    <option value="confirmed">{{ t('booking.status_confirmed') }}</option>
                                    <option value="cancelled">{{ t('booking.status_cancelled') }}</option>
                                    <option value="completed">{{ t('booking.status_completed') }}</option>
                                    <option value="no_show">{{ t('booking.status_no_show') }}</option>
                                </select>
                            </td>
                            <td class="px-4 py-3">{{ money(row.price_cents) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="grid gap-3 md:hidden">
                <article v-for="row in bookings.data" :key="row.id" class="surface-card p-4">
                    <p class="font-medium">{{ row.client }}</p>
                    <p class="text-sm text-muted">{{ row.service }} · {{ row.staff }}</p>
                    <p class="text-sm">{{ format(row.starts_at) }} · {{ money(row.price_cents) }}</p>
                    <select :value="row.status" class="mt-3 w-full rounded-lg border-line bg-surface text-sm" @change="update(row.id, $event.target.value)">
                        <option value="confirmed">{{ t('booking.status_confirmed') }}</option>
                        <option value="cancelled">{{ t('booking.status_cancelled') }}</option>
                        <option value="completed">{{ t('booking.status_completed') }}</option>
                        <option value="no_show">{{ t('booking.status_no_show') }}</option>
                    </select>
                </article>
            </div>
            <p v-if="!bookings.data.length" class="mt-6 text-sm text-muted">{{ t('common.empty') }}</p>
        </div>
    </AuthenticatedLayout>
</template>
