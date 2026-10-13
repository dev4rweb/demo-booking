<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

defineProps({ services: Array });
const { t, locRoute } = useI18n();
const form = useForm({ name: '', duration_minutes: 60, price_cents: 4000, is_active: true });
const money = (cents) => `$${(cents / 100).toFixed(2)}`;

const create = () => form.post(locRoute('services.store'), { onSuccess: () => form.reset() });
const toggle = (service) => {
    const f = useForm({ ...service, is_active: !service.is_active });
    f.patch(locRoute('services.update', { service: service.id }));
};
const destroy = (service) => {
    if (confirm(t('common.delete') + '?')) {
        useForm({}).delete(locRoute('services.destroy', { service: service.id }));
    }
};
</script>

<template>
    <Head :title="t('services.title')" />
    <AuthenticatedLayout>
        <template #header><h2 class="text-xl font-semibold">{{ t('services.title') }}</h2></template>
        <div class="mx-auto grid max-w-5xl gap-6 px-4 py-6 sm:px-6 lg:grid-cols-3">
            <form class="surface-card p-5 lg:col-span-1" @submit.prevent="create">
                <h3 class="font-semibold">{{ t('services.add') }}</h3>
                <div class="mt-4 space-y-3">
                    <div>
                        <InputLabel :value="t('common.name')" />
                        <TextInput v-model="form.name" class="mt-1 block w-full" required />
                    </div>
                    <div>
                        <InputLabel :value="t('common.duration')" />
                        <TextInput v-model="form.duration_minutes" type="number" class="mt-1 block w-full" required />
                    </div>
                    <div>
                        <InputLabel :value="t('services.price_usd')" />
                        <TextInput :model-value="String(form.price_cents / 100)" class="mt-1 block w-full" @input="form.price_cents = Math.round(Number($event.target.value || 0) * 100)" />
                    </div>
                    <PrimaryButton :disabled="form.processing">{{ t('common.add') }}</PrimaryButton>
                </div>
            </form>
            <div class="grid gap-3 lg:col-span-2">
                <article v-for="service in services" :key="service.id" class="surface-card flex flex-col gap-2 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="font-medium">{{ service.name }}</p>
                        <p class="text-sm text-muted">{{ service.duration_minutes }} {{ t('common.minutes') }} · {{ money(service.price_cents) }}</p>
                    </div>
                    <div class="flex gap-3 text-sm">
                        <button type="button" class="text-accent" @click="toggle(service)">{{ service.is_active ? t('common.active') : t('common.inactive') }}</button>
                        <button type="button" class="text-danger" @click="destroy(service)">{{ t('common.delete') }}</button>
                    </div>
                </article>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
