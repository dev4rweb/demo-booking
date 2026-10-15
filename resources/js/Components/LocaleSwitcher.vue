<script setup>
import { ref } from 'vue';
import { useI18n } from '@/composables/useI18n';

const { t, locale, locales, switchLocale } = useI18n();
const open = ref(false);
</script>

<template>
    <div class="relative">
        <button
            type="button"
            class="inline-flex h-10 items-center gap-2 rounded-full border border-line bg-surface px-3 text-sm font-medium text-ink transition duration-200 hover:border-accent hover:text-accent focus:outline-none focus-visible:ring-2 focus-visible:ring-accent"
            :aria-label="t('theme.language')"
            :aria-expanded="open"
            @click="open = !open"
        >
            <span class="uppercase tracking-wide">{{ locale }}</span>
            <svg class="h-3.5 w-3.5 text-muted" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
            </svg>
        </button>

        <div
            v-if="open"
            class="fixed inset-0 z-40"
            @click="open = false"
        />

        <div
            v-if="open"
            class="absolute end-0 z-50 mt-2 min-w-40 overflow-hidden rounded-xl border border-line bg-surface py-1 shadow-glow"
        >
            <button
                v-for="item in locales"
                :key="item.code"
                type="button"
                class="flex w-full items-center justify-between px-3 py-2 text-start text-sm transition hover:bg-surface-2"
                :class="item.code === locale ? 'text-accent' : 'text-ink'"
                @click="switchLocale(item.code); open = false"
            >
                <span>{{ item.name }}</span>
                <span class="text-xs uppercase text-muted">{{ item.code }}</span>
            </button>
        </div>
    </div>
</template>
