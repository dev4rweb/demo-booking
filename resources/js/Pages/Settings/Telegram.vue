<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, router } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

defineProps({
    connected: Boolean,
    chatId: String,
    connectToken: String,
    botUsername: String,
    enabled: Boolean,
});

const { t, locRoute } = useI18n();
</script>

<template>
    <Head :title="t('telegram.title')" />
    <AuthenticatedLayout>
        <template #header><h2 class="text-xl font-semibold">{{ t('telegram.title') }}</h2></template>
        <div class="mx-auto max-w-xl px-4 py-6 sm:px-6">
            <div class="surface-card p-6">
                <p v-if="!enabled" class="mb-4 rounded-xl border border-line bg-surface-2 px-3 py-2 text-sm text-warn">{{ t('telegram.disabled') }}</p>
                <p class="text-sm leading-7 text-muted">{{ t('telegram.hint') }}</p>
                <p class="mt-4 text-sm">
                    <span class="text-muted">{{ t('telegram.connected') }}:</span>
                    {{ connected ? chatId : t('telegram.not_connected') }}
                </p>
                <p v-if="connectToken" class="mt-3 rounded-xl border border-line bg-surface-2 px-3 py-2 font-mono text-sm">/start {{ connectToken }}</p>
                <a v-if="botUsername" :href="`https://t.me/${botUsername}`" class="mt-3 inline-block text-sm text-accent" target="_blank">@{{ botUsername }}</a>
                <div class="mt-6 flex flex-wrap gap-3">
                    <PrimaryButton type="button" @click="router.post(locRoute('telegram.connect'))">{{ t('telegram.generate') }}</PrimaryButton>
                    <SecondaryButton v-if="connected" type="button" @click="router.post(locRoute('telegram.disconnect'))">{{ t('telegram.disconnect') }}</SecondaryButton>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
