<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import LocaleSwitcher from '@/Components/LocaleSwitcher.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

const { t, locRoute } = useI18n();
</script>

<template>
    <header class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-5 sm:px-6">
        <Link :href="locRoute('welcome')" class="flex min-w-0 items-center gap-2">
            <ApplicationLogo class="h-9 w-9 shrink-0" />
            <span class="truncate text-sm font-semibold tracking-tight">{{ t('app_name') }}</span>
        </Link>
        <nav class="flex flex-wrap items-center justify-end gap-1 sm:gap-2" :aria-label="t('nav.menu')">
            <Link :href="locRoute('about')" class="rounded-full px-2.5 py-2 text-xs text-muted transition hover:text-ink sm:px-3 sm:text-sm">{{ t('nav.about') }}</Link>
            <Link :href="locRoute('guide')" class="rounded-full px-2.5 py-2 text-xs text-muted transition hover:text-ink sm:px-3 sm:text-sm">{{ t('nav.guide') }}</Link>
            <Link :href="locRoute('login')" class="hidden rounded-full px-3 py-2 text-sm text-muted transition hover:text-ink sm:inline">{{ t('nav.login') }}</Link>
            <ThemeToggle />
            <LocaleSwitcher />
        </nav>
    </header>
</template>
