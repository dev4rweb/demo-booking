<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

defineProps({ tenants: Array });
const { t } = useI18n();
</script>

<template>
    <Head :title="t('admin.tenants')" />
    <AuthenticatedLayout>
        <template #header><h2 class="text-xl font-semibold">{{ t('admin.tenants') }}</h2></template>
        <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6">
            <div class="grid gap-3">
                <article v-for="tenant in tenants" :key="tenant.id" class="surface-card p-4 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="font-medium">{{ tenant.name }}</p>
                        <p class="text-sm text-muted">/b/{{ tenant.slug }} · {{ tenant.plan }}</p>
                    </div>
                    <p class="mt-2 text-sm text-muted sm:mt-0">{{ tenant.staff_members_count }} {{ t('nav.staff') }} · {{ tenant.bookings_count }} {{ t('nav.bookings') }}</p>
                </article>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
