<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import LocaleSwitcher from '@/Components/LocaleSwitcher.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

const { locRoute } = useI18n();
</script>

<template>
    <div class="relative flex min-h-screen flex-col bg-canvas px-4 py-6 sm:items-center sm:justify-center sm:py-12">
        <div class="mb-6 flex w-full max-w-md items-center justify-between sm:justify-center sm:gap-3">
            <Link :href="locRoute('welcome')" class="flex items-center gap-2">
                <ApplicationLogo class="h-12 w-12" />
            </Link>
            <div class="flex items-center gap-2 sm:absolute sm:end-6 sm:top-6">
                <ThemeToggle />
                <LocaleSwitcher />
            </div>
        </div>

        <div class="w-full overflow-hidden border border-line bg-surface px-5 py-6 shadow-card sm:max-w-md sm:rounded-2xl sm:px-8">
            <slot />
        </div>
    </div>
</template>
