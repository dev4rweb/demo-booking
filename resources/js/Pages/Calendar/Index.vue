<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from '@/composables/useI18n';

const props = defineProps({
    view: String,
    date: String,
    timezone: String,
    events: Array,
    staff: Array,
});

const { t, locRoute } = useI18n();

const go = (view, date) => {
    router.get(locRoute('calendar'), { view, date }, { preserveState: true, preserveScroll: true });
};

const shift = (days) => {
    const d = new Date(props.date + 'T00:00:00');
    d.setDate(d.getDate() + days);
    go(props.view, d.toISOString().slice(0, 10));
};

const today = () => go(props.view, new Date().toISOString().slice(0, 10));

const hours = computed(() => {
    const list = [];
    for (let h = 7; h <= 20; h++) list.push(h);
    return list;
});

const days = computed(() => {
    const start = new Date(props.date + 'T00:00:00');
    if (props.view === 'week') {
        const dow = start.getDay();
        const mondayOffset = dow === 0 ? -6 : 1 - dow;
        start.setDate(start.getDate() + mondayOffset);
    }
    const count = props.view === 'day' ? 1 : 7;
    return Array.from({ length: count }, (_, i) => {
        const d = new Date(start);
        d.setDate(start.getDate() + i);
        return d;
    });
});

const eventsOn = (day, hour) => {
    return props.events.filter((event) => {
        const start = new Date(event.starts_at);
        return start.toDateString() === day.toDateString() && start.getHours() === hour;
    });
};

const label = (day) => day.toLocaleDateString(undefined, { weekday: 'short', month: 'short', day: 'numeric' });
</script>

<template>
    <Head :title="t('calendar.title')" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold tracking-tight">{{ t('calendar.title') }}</h2>
                    <p class="mt-1 text-sm text-muted">{{ t('calendar.hint') }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" class="rounded-full border border-line px-3 py-1.5 text-sm" @click="shift(view === 'day' ? -1 : -7)">‹</button>
                    <button type="button" class="rounded-full border border-line px-3 py-1.5 text-sm" @click="today">{{ t('common.today') }}</button>
                    <button type="button" class="rounded-full border border-line px-3 py-1.5 text-sm" @click="shift(view === 'day' ? 1 : 7)">›</button>
                    <button type="button" class="rounded-full px-3 py-1.5 text-sm" :class="view === 'day' ? 'bg-accent text-accent-fg' : 'border border-line'" @click="go('day', date)">{{ t('common.day') }}</button>
                    <button type="button" class="rounded-full px-3 py-1.5 text-sm" :class="view === 'week' ? 'bg-accent text-accent-fg' : 'border border-line'" @click="go('week', date)">{{ t('common.week') }}</button>
                </div>
            </div>
        </template>
        <div class="mx-auto max-w-7xl overflow-x-auto px-4 py-6 sm:px-6">
            <div class="min-w-[320px]" :class="view === 'week' ? 'min-w-[720px]' : ''">
                <div class="grid" :style="{ gridTemplateColumns: `3.5rem repeat(${days.length}, minmax(0, 1fr))` }">
                    <div />
                    <div v-for="day in days" :key="day.toISOString()" class="border-b border-line px-2 py-2 text-center text-xs font-medium text-muted">{{ label(day) }}</div>
                    <template v-for="hour in hours" :key="hour">
                        <div class="border-t border-line py-3 text-right text-[11px] text-muted pe-2">{{ String(hour).padStart(2, '0') }}:00</div>
                        <div v-for="day in days" :key="day.toISOString() + hour" class="min-h-16 border-s border-t border-line px-1 py-1">
                            <article v-for="event in eventsOn(day, hour)" :key="event.id" class="mb-1 rounded-lg bg-surface-2 px-2 py-1 text-[11px] leading-4">
                                <p class="font-medium text-ink">{{ event.client }}</p>
                                <p class="text-muted">{{ event.service }} · {{ event.staff }}</p>
                            </article>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
