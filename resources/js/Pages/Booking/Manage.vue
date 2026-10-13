<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import LocaleSwitcher from '@/Components/LocaleSwitcher.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from '@/composables/useI18n';

const props = defineProps({
    booking: Object,
    staff: Array,
});

const { t, locRoute } = useI18n();
const page = usePage();
const date = ref(props.booking.starts_at.slice(0, 10));
const staffId = ref(props.booking.staff_member_id);
const slots = ref([]);
const selected = ref(null);

const loadSlots = async () => {
    const url = locRoute('bookings.manage.slots', { booking: props.booking.id });
    const params = new URLSearchParams({ staff_member_id: String(staffId.value), date: date.value, token: props.booking.token });
    const res = await fetch(`${url}?${params}`, { headers: { Accept: 'application/json' } });
    slots.value = (await res.json()).slots || [];
};

const tokenQs = () => `?token=${encodeURIComponent(props.booking.token)}`;

const cancel = () => {
    router.post(`${locRoute('bookings.manage.cancel', { booking: props.booking.id })}${tokenQs()}`);
};

const reschedule = () => {
    if (!selected.value) return;
    router.post(`${locRoute('bookings.manage.reschedule', { booking: props.booking.id })}${tokenQs()}`, {
        staff_member_id: selected.value.staff_member_id,
        starts_at: selected.value.start,
    });
};

const format = (iso) => new Date(iso).toLocaleString();
const timeLabel = (iso) => new Date(iso).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
</script>

<template>
    <Head :title="t('booking.manage_title')" />
    <div class="min-h-screen">
        <header class="mx-auto flex max-w-xl items-center justify-between px-4 py-5">
            <Link :href="locRoute('welcome')" class="flex items-center gap-2">
                <ApplicationLogo class="h-9 w-9" />
            </Link>
            <div class="flex gap-2"><ThemeToggle /><LocaleSwitcher /></div>
        </header>
        <main class="mx-auto max-w-xl px-4 pb-16">
            <p v-if="page.props.flash?.success" class="mb-4 text-sm text-good">{{ page.props.flash.success }}</p>
            <div class="surface-card p-6">
                <h1 class="text-2xl font-semibold">{{ t('booking.manage_title') }}</h1>
                <p class="mt-2 text-sm text-muted">{{ booking.service }} · {{ booking.staff }} · {{ format(booking.starts_at) }}</p>
                <p class="mt-1 text-xs uppercase tracking-wide text-accent">{{ t('booking.status_' + booking.status) }}</p>

                <div v-if="booking.status === 'confirmed'" class="mt-6 space-y-4">
                    <div>
                        <label class="text-sm font-medium">{{ t('booking.pick_staff') }}</label>
                        <select v-model="staffId" class="mt-1 w-full rounded-xl border-line bg-surface text-ink">
                            <option v-for="member in staff" :key="member.id" :value="member.id">{{ member.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium">{{ t('booking.pick_date') }}</label>
                        <input v-model="date" type="date" class="mt-1 w-full rounded-xl border-line bg-surface text-ink" @change="loadSlots" />
                    </div>
                    <SecondaryButton type="button" @click="loadSlots">{{ t('booking.pick_slot') }}</SecondaryButton>
                    <div class="grid grid-cols-2 gap-2">
                        <button v-for="slot in slots" :key="slot.start" type="button" class="rounded-xl border px-3 py-2 text-sm" :class="selected?.start === slot.start ? 'border-accent text-accent' : 'border-line'" @click="selected = slot">
                            {{ timeLabel(slot.start) }}
                        </button>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <PrimaryButton type="button" :disabled="!selected" @click="reschedule">{{ t('booking.reschedule') }}</PrimaryButton>
                        <button type="button" class="text-sm text-danger" @click="cancel">{{ t('booking.cancel_cta') }}</button>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
