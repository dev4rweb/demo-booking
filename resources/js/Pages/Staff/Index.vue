<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { reactive } from 'vue';
import { useI18n } from '@/composables/useI18n';

const props = defineProps({
    staff: Array,
    services: Array,
    canAdd: Boolean,
    staffLimit: [Number, null],
    plan: String,
});

const { t, locRoute } = useI18n();
const form = useForm({ name: '', email: '', service_ids: [] });
const editors = reactive({});

const ensureEditor = (member) => {
    if (!editors[member.id]) {
        editors[member.id] = {
            name: member.name,
            is_active: member.is_active,
            service_ids: member.services?.map((s) => s.id) || [],
            hours: (member.working_hours || []).map((h) => ({
                weekday: h.weekday,
                starts_at: String(h.starts_at).slice(0, 5),
                ends_at: String(h.ends_at).slice(0, 5),
            })),
            days_off: (member.days_off || []).map((d) => d.date?.slice?.(0, 10) || d.date),
            newOff: '',
        };
    }
    return editors[member.id];
};

const create = () => form.post(locRoute('staff.store'), { onSuccess: () => form.reset() });
const save = (member) => {
    const data = ensureEditor(member);
    useForm(data).patch(locRoute('staff.update', { staff: member.id }));
};
const destroy = (member) => {
    if (confirm(t('common.delete') + '?')) useForm({}).delete(locRoute('staff.destroy', { staff: member.id }));
};
const addHour = (member) => ensureEditor(member).hours.push({ weekday: 1, starts_at: '09:00', ends_at: '18:00' });
const addOff = (member) => {
    const ed = ensureEditor(member);
    if (ed.newOff) {
        ed.days_off.push(ed.newOff);
        ed.newOff = '';
    }
};
</script>

<template>
    <Head :title="t('staff.title')" />
    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold">{{ t('staff.title') }}</h2>
                <p class="text-sm text-muted">{{ t('billing.staff_limit') }}: {{ staffLimit ?? t('billing.unlimited') }} ({{ plan }})</p>
            </div>
        </template>
        <div class="mx-auto max-w-5xl space-y-6 px-4 py-6 sm:px-6">
            <form v-if="canAdd" class="surface-card grid gap-3 p-5 sm:grid-cols-2" @submit.prevent="create">
                <h3 class="sm:col-span-2 font-semibold">{{ t('staff.add') }}</h3>
                <div>
                    <InputLabel :value="t('common.name')" />
                    <TextInput v-model="form.name" class="mt-1 block w-full" required />
                </div>
                <div>
                    <InputLabel :value="t('staff.login_email')" />
                    <TextInput v-model="form.email" type="email" class="mt-1 block w-full" />
                </div>
                <PrimaryButton class="sm:col-span-2" :disabled="form.processing">{{ t('common.add') }}</PrimaryButton>
            </form>
            <p v-else class="text-sm text-warn">{{ t('staff.limit_reached') }}</p>

            <article v-for="member in staff" :key="member.id" class="surface-card p-5">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="font-semibold">{{ member.name }}</h3>
                    <button type="button" class="text-sm text-danger" @click="destroy(member)">{{ t('common.delete') }}</button>
                </div>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <TextInput v-model="ensureEditor(member).name" />
                    <div class="flex flex-wrap gap-2">
                        <label v-for="service in services" :key="service.id" class="text-sm">
                            <input type="checkbox" :value="service.id" v-model="ensureEditor(member).service_ids" class="rounded border-line text-accent" />
                            {{ service.name }}
                        </label>
                    </div>
                </div>
                <h4 class="mt-4 text-sm font-semibold text-muted">{{ t('staff.hours') }}</h4>
                <div class="mt-2 space-y-2">
                    <div v-for="(hour, idx) in ensureEditor(member).hours" :key="idx" class="grid grid-cols-3 gap-2">
                        <select v-model.number="hour.weekday" class="rounded-xl border-line bg-surface text-sm">
                            <option v-for="d in [0,1,2,3,4,5,6]" :key="d" :value="d">{{ t('weekdays.' + d) }}</option>
                        </select>
                        <input v-model="hour.starts_at" type="time" class="rounded-xl border-line bg-surface text-sm" />
                        <input v-model="hour.ends_at" type="time" class="rounded-xl border-line bg-surface text-sm" />
                    </div>
                    <button type="button" class="text-sm text-accent" @click="addHour(member)">{{ t('common.add') }}</button>
                </div>
                <h4 class="mt-4 text-sm font-semibold text-muted">{{ t('staff.days_off') }}</h4>
                <div class="mt-2 flex flex-wrap gap-2">
                    <span v-for="(off, idx) in ensureEditor(member).days_off" :key="off + idx" class="rounded-full border border-line px-3 py-1 text-xs">
                        {{ off }}
                        <button type="button" class="ms-1 text-danger" @click="ensureEditor(member).days_off.splice(idx, 1)">×</button>
                    </span>
                    <input v-model="ensureEditor(member).newOff" type="date" class="rounded-xl border-line bg-surface text-sm" />
                    <button type="button" class="text-sm text-accent" @click="addOff(member)">{{ t('common.add') }}</button>
                </div>
                <PrimaryButton class="mt-4" type="button" @click="save(member)">{{ t('common.save') }}</PrimaryButton>
            </article>
        </div>
    </AuthenticatedLayout>
</template>
