<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

const props = defineProps({
    plans: Object,
    currentPlan: String,
    demoMode: Boolean,
});

const { t, locRoute } = useI18n();
const money = (cents) => cents ? `$${(cents / 100).toFixed(0)}` : '$0';

const choose = (key) => {
    useForm({ plan: key }).post(locRoute('billing.checkout'));
};
</script>

<template>
    <Head :title="t('billing.title')" />
    <AuthenticatedLayout>
        <template #header><h2 class="text-xl font-semibold">{{ t('billing.title') }}</h2></template>
        <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6">
            <p class="rounded-2xl border border-line bg-surface-2 px-4 py-3 text-sm" :class="demoMode ? 'text-warn' : 'text-muted'">
                {{ demoMode ? t('billing.demo_notice') : t('billing.stripe_notice') }}
            </p>
            <p class="mt-4 text-sm text-muted">{{ t('billing.current') }}: <strong class="capitalize text-ink">{{ currentPlan }}</strong></p>
            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <article v-for="(plan, key) in plans" :key="key" class="surface-card flex flex-col p-5" :class="currentPlan === key ? 'ring-1 ring-accent' : ''">
                    <h3 class="text-lg font-semibold">{{ plan.name }}</h3>
                    <p class="mt-2 text-3xl font-semibold">{{ money(plan.price_cents) }}<span class="text-sm font-normal text-muted">{{ t('billing.per_month') }}</span></p>
                    <p class="mt-2 text-sm text-muted">{{ t('billing.staff_limit') }}: {{ plan.staff_limit ?? t('billing.unlimited') }}</p>
                    <ul class="mt-4 flex-1 space-y-2 text-sm text-muted">
                        <li v-for="feature in plan.features" :key="feature">{{ feature }}</li>
                    </ul>
                    <PrimaryButton class="mt-6" type="button" :disabled="currentPlan === key" @click="choose(key)">{{ t('billing.choose') }}</PrimaryButton>
                </article>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
