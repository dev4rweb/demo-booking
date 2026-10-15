<script setup>
import { useI18n } from '@/composables/useI18n';
import { useTheme } from '@/composables/useTheme';

const { t } = useI18n();
const { theme, toggle } = useTheme();
</script>

<template>
    <button
        type="button"
        class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-line bg-surface text-ink transition duration-200 hover:border-accent hover:text-accent focus:outline-none focus-visible:ring-2 focus-visible:ring-accent"
        :aria-label="theme === 'dark' ? t('theme.to_light') : t('theme.to_dark')"
        @click="toggle"
    >
        <svg v-if="theme === 'dark'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 4v1.5M12 18.5V20M4 12h1.5M18.5 12H20M6.1 6.1l1.1 1.1M16.8 16.8l1.1 1.1M6.1 17.9l1.1-1.1M16.8 7.2l1.1-1.1M12 8a4 4 0 100 8 4 4 0 000-8z" />
        </svg>
        <svg v-else class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M21 14.3A8.4 8.4 0 1110.1 3 6.6 6.6 0 0021 14.3z" />
        </svg>
    </button>
</template>
