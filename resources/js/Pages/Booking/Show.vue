<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import LocaleSwitcher from '@/Components/LocaleSwitcher.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { useI18n } from '@/composables/useI18n';

const props = defineProps({
    business: Object,
    services: Array,
    staff: Array,
});

const { t, locRoute } = useI18n();
const step = ref(1);
const serviceId = ref(null);
const staffId = ref(null);
const date = ref(new Date().toISOString().slice(0, 10));
const slots = ref([]);
const loadingSlots = ref(false);
const selectedSlot = ref(null);

const service = computed(() => props.services.find((s) => s.id === serviceId.value));
const eligibleStaff = computed(() => {
    if (!serviceId.value) return [];
    return props.staff.filter((m) => m.service_ids.includes(serviceId.value));
});

const form = useForm({
    service_id: null,
    staff_member_id: null,
    starts_at: '',
    name: '',
    phone: '',
    email: '',
});

watch([serviceId, staffId, date], async () => {
    selectedSlot.value = null;
    slots.value = [];
    if (!serviceId.value || !date.value) return;
    loadingSlots.value = true;
    const params = new URLSearchParams({
        service_id: String(serviceId.value),
        date: date.value,
    });
    if (staffId.value) params.set('staff_member_id', String(staffId.value));
    const url = locRoute('booking.slots', { slug: props.business.slug });
    const res = await fetch(`${url}?${params.toString()}`, { headers: { Accept: 'application/json' } });
    const json = await res.json();
    slots.value = json.slots || [];
    loadingSlots.value = false;
});

const pickService = (id) => {
    serviceId.value = id;
    staffId.value = null;
    step.value = 2;
};

const pickStaff = (id) => {
    staffId.value = id;
    step.value = 3;
};

const pickSlot = (slot) => {
    selectedSlot.value = slot;
    step.value = 4;
};

const money = (cents) => `$${(cents / 100).toFixed(2)}`;
const timeLabel = (iso) => new Date(iso).toLocaleTimeString('en-GB', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
    timeZone: props.business.timezone,
});

const submit = () => {
    form.service_id = serviceId.value;
    form.staff_member_id = selectedSlot.value.staff_member_id;
    form.starts_at = selectedSlot.value.start;
    form.post(locRoute('booking.store', { slug: props.business.slug }));
};
</script>

<template>
    <Head :title="business.name" />
    <div class="min-h-screen">
        <header class="mx-auto flex max-w-3xl items-center justify-between gap-3 px-4 py-5">
            <Link :href="locRoute('welcome')" class="flex items-center gap-2">
                <ApplicationLogo class="h-9 w-9" />
                <span class="text-sm font-semibold">{{ business.name }}</span>
            </Link>
            <div class="flex items-center gap-2">
                <ThemeToggle />
                <LocaleSwitcher />
            </div>
        </header>

        <main class="mx-auto max-w-3xl px-4 pb-16">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent">{{ t('booking.title') }}</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight">{{ business.name }}</h1>
            <p class="mt-2 text-sm text-muted">{{ t('booking.timezone') }} {{ business.timezone }}</p>

            <ol class="mt-6 flex flex-wrap gap-2 text-xs font-medium">
                <li v-for="(label, i) in [t('booking.step_service'), t('booking.step_staff'), t('booking.step_slot'), t('booking.step_confirm')]" :key="i" class="rounded-full px-3 py-1" :class="step === i + 1 ? 'bg-accent text-accent-fg' : 'border border-line text-muted'">
                    {{ i + 1 }}. {{ label }}
                </li>
            </ol>

            <section v-if="step === 1" class="mt-6 grid gap-3">
                <h2 class="text-lg font-semibold">{{ t('booking.pick_service') }}</h2>
                <button v-for="item in services" :key="item.id" type="button" class="soft-ring surface-card flex items-center justify-between p-4 text-start" @click="pickService(item.id)">
                    <span>
                        <span class="block font-medium">{{ item.name }}</span>
                        <span class="text-sm text-muted">{{ item.duration_minutes }} {{ t('common.minutes') }}</span>
                    </span>
                    <span class="font-semibold text-accent">{{ money(item.price_cents) }}</span>
                </button>
            </section>

            <section v-if="step === 2" class="mt-6 grid gap-3">
                <h2 class="text-lg font-semibold">{{ t('booking.pick_staff') }}</h2>
                <button type="button" class="soft-ring surface-card p-4 text-start" @click="pickStaff(null)">{{ t('common.any') }}</button>
                <button v-for="member in eligibleStaff" :key="member.id" type="button" class="soft-ring surface-card p-4 text-start" @click="pickStaff(member.id)">{{ member.name }}</button>
                <button type="button" class="text-sm text-muted" @click="step = 1">{{ t('common.previous') }}</button>
            </section>

            <section v-if="step === 3" class="mt-6">
                <h2 class="text-lg font-semibold">{{ t('booking.pick_date') }}</h2>
                <input v-model="date" type="date" class="mt-3 w-full rounded-xl border-line bg-surface text-ink focus:border-accent focus:ring-accent sm:w-56" />
                <h3 class="mt-5 text-sm font-semibold uppercase tracking-wide text-muted">{{ t('booking.pick_slot') }}</h3>
                <p v-if="loadingSlots" class="mt-3 text-sm text-muted">{{ t('common.loading') }}</p>
                <p v-else-if="!slots.length" class="mt-3 text-sm text-muted">{{ t('booking.no_slots') }}</p>
                <div v-else class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-4">
                    <button v-for="slot in slots" :key="slot.start" type="button" class="rounded-xl border border-line px-3 py-2 text-sm transition hover:border-accent" @click="pickSlot(slot)">
                        {{ timeLabel(slot.start) }}
                    </button>
                </div>
                <button type="button" class="mt-4 text-sm text-muted" @click="step = 2">{{ t('common.previous') }}</button>
            </section>

            <section v-if="step === 4 && selectedSlot" class="mt-6 surface-card p-5">
                <h2 class="text-lg font-semibold">{{ t('booking.step_confirm') }}</h2>
                <p class="mt-2 text-sm text-muted">{{ service?.name }} · {{ timeLabel(selectedSlot.start) }} · {{ money(service?.price_cents || 0) }}</p>
                <form class="mt-4 space-y-4" @submit.prevent="submit">
                    <div>
                        <InputLabel for="guest-name" :value="t('booking.your_name')" />
                        <TextInput id="guest-name" v-model="form.name" class="mt-1 block w-full" required autocomplete="name" />
                        <InputError class="mt-1" :message="form.errors.name" />
                    </div>
                    <div>
                        <InputLabel for="guest-phone" :value="t('common.phone')" />
                        <TextInput id="guest-phone" v-model="form.phone" class="mt-1 block w-full" required autocomplete="tel" />
                        <InputError class="mt-1" :message="form.errors.phone" />
                    </div>
                    <div>
                        <InputLabel for="guest-email" :value="t('common.email')" />
                        <TextInput id="guest-email" v-model="form.email" type="email" class="mt-1 block w-full" required autocomplete="email" />
                        <InputError class="mt-1" :message="form.errors.email || form.errors.starts_at" />
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <PrimaryButton :disabled="form.processing">{{ t('booking.confirm_cta') }}</PrimaryButton>
                        <button type="button" class="text-sm text-muted" @click="step = 3">{{ t('common.previous') }}</button>
                    </div>
                </form>
            </section>
        </main>
    </div>
</template>
