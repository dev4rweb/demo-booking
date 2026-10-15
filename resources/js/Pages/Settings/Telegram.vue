<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, router } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

defineProps({
    connected: Boolean,
    deepLink: { type: String, default: null },
    enabled: Boolean,
    scope: { type: String, default: 'business' },
});

const { t, locRoute } = useI18n();
</script>

<template>
    <Head :title="t('telegram.title')" />
    <AuthenticatedLayout>
        <template #header><h2 class="text-xl font-semibold">{{ t('telegram.title') }}</h2></template>
        <div class="mx-auto max-w-xl px-4 py-6 sm:px-6">
            <div class="surface-card p-6">
                <p v-if="!enabled" class="mb-4 rounded-xl border border-line bg-surface-2 px-3 py-2 text-sm text-muted">{{ t('telegram.disabled') }}</p>
                <p v-else class="text-sm leading-7 text-muted">{{ scope === 'staff' ? t('telegram.hint_staff') : t('telegram.hint') }}</p>
                <p class="mt-4 text-sm font-medium text-ink">
                    {{ connected ? t('telegram.connected') : t('telegram.not_connected') }}
                </p>
                <p v-if="connected" class="mt-2 text-sm leading-6 text-muted">{{ t('telegram.connected_body') }}</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a
                        v-if="enabled && !connected && deepLink"
                        :href="deepLink"
                        target="_blank"
                        rel="noopener"
                        class="inline-flex items-center rounded-xl bg-accent px-4 py-2.5 text-sm font-semibold text-accent-fg"
                    >
                        {{ t('telegram.connect') }}
                    </a>
                    <SecondaryButton v-if="connected" type="button" @click="router.post(locRoute('telegram.disconnect'))">{{ t('telegram.disconnect') }}</SecondaryButton>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
