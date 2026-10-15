<script setup>
import { computed, ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import LocaleSwitcher from '@/Components/LocaleSwitcher.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

const { t, locRoute } = useI18n();
const page = usePage();
const showingNavigationDropdown = ref(false);

const role = computed(() => page.props.auth?.user?.role);
const isOwner = computed(() => ['owner', 'platform_admin'].includes(role.value));
const isAdmin = computed(() => role.value === 'platform_admin');
const canTelegram = computed(() => ['owner', 'staff', 'platform_admin'].includes(role.value));
const demoMode = computed(() => page.props.demoMode);
</script>

<template>
    <div class="min-h-screen bg-canvas">
        <nav class="border-b border-line/80 bg-surface/80 backdrop-blur-md">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between gap-3">
                    <div class="flex min-w-0 items-center gap-3">
                        <Link :href="locRoute('dashboard')" class="flex shrink-0 items-center gap-2">
                            <ApplicationLogo class="h-9 w-9" />
                            <span class="hidden text-sm font-semibold tracking-tight text-ink sm:inline">{{ t('app_name') }}</span>
                        </Link>
                        <div class="hidden items-center gap-5 xl:ms-8 xl:flex">
                            <NavLink :href="locRoute('dashboard')" :active="route().current('dashboard') || route().current('locale.dashboard')">{{ t('nav.dashboard') }}</NavLink>
                            <NavLink :href="locRoute('calendar')" :active="route().current('calendar') || route().current('locale.calendar')">{{ t('nav.calendar') }}</NavLink>
                            <NavLink :href="locRoute('bookings.index')" :active="route().current('bookings.*') || route().current('locale.bookings.*')">{{ t('nav.bookings') }}</NavLink>
                            <NavLink v-if="isOwner" :href="locRoute('clients.index')" :active="route().current('clients.*') || route().current('locale.clients.*')">{{ t('nav.clients') }}</NavLink>
                            <NavLink v-if="isOwner" :href="locRoute('services.index')" :active="route().current('services.*') || route().current('locale.services.*')">{{ t('nav.services') }}</NavLink>
                            <NavLink v-if="isOwner" :href="locRoute('staff.index')" :active="route().current('staff.*') || route().current('locale.staff.*')">{{ t('nav.staff') }}</NavLink>
                            <NavLink v-if="isAdmin" :href="locRoute('admin.tenants')" :active="route().current('admin.*') || route().current('locale.admin.*')">{{ t('nav.tenants') }}</NavLink>
                        </div>
                    </div>
                    <div class="hidden items-center gap-2 xl:flex">
                        <span v-if="demoMode" class="rounded-full border border-line px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide text-accent">{{ t('common.demo_mode') }}</span>
                        <ThemeToggle />
                        <LocaleSwitcher />
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button type="button" class="inline-flex items-center rounded-full border border-line bg-surface px-3 py-2 text-sm font-medium text-ink transition hover:border-accent">
                                    {{ $page.props.auth.user.name }}
                                    <svg class="-me-0.5 ms-2 h-4 w-4 text-muted" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </template>
                            <template #content>
                                <DropdownLink v-if="isOwner" :href="locRoute('billing.index')">{{ t('nav.billing') }}</DropdownLink>
                                <DropdownLink v-if="canTelegram" :href="locRoute('telegram.index')">{{ t('nav.telegram') }}</DropdownLink>
                                <DropdownLink :href="locRoute('about')">{{ t('nav.about') }}</DropdownLink>
                                <DropdownLink :href="locRoute('guide')">{{ t('nav.guide') }}</DropdownLink>
                                <DropdownLink :href="locRoute('profile.edit')">{{ t('nav.profile') }}</DropdownLink>
                                <DropdownLink :href="locRoute('logout')" method="post" as="button">{{ t('nav.logout') }}</DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                    <div class="flex items-center gap-2 xl:hidden">
                        <ThemeToggle />
                        <LocaleSwitcher />
                        <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-line text-ink" :aria-label="showingNavigationDropdown ? t('nav.close_menu') : t('nav.menu')" @click="showingNavigationDropdown = !showingNavigationDropdown">
                            <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div :class="showingNavigationDropdown ? 'block' : 'hidden'" class="border-t border-line xl:hidden">
                <div class="space-y-1 py-2">
                    <ResponsiveNavLink :href="locRoute('dashboard')">{{ t('nav.dashboard') }}</ResponsiveNavLink>
                    <ResponsiveNavLink :href="locRoute('calendar')">{{ t('nav.calendar') }}</ResponsiveNavLink>
                    <ResponsiveNavLink :href="locRoute('bookings.index')">{{ t('nav.bookings') }}</ResponsiveNavLink>
                    <ResponsiveNavLink v-if="isOwner" :href="locRoute('clients.index')">{{ t('nav.clients') }}</ResponsiveNavLink>
                    <ResponsiveNavLink v-if="isOwner" :href="locRoute('services.index')">{{ t('nav.services') }}</ResponsiveNavLink>
                    <ResponsiveNavLink v-if="isOwner" :href="locRoute('staff.index')">{{ t('nav.staff') }}</ResponsiveNavLink>
                    <ResponsiveNavLink v-if="isOwner" :href="locRoute('billing.index')">{{ t('nav.billing') }}</ResponsiveNavLink>
                    <ResponsiveNavLink v-if="canTelegram" :href="locRoute('telegram.index')">{{ t('nav.telegram') }}</ResponsiveNavLink>
                    <ResponsiveNavLink v-if="isAdmin" :href="locRoute('admin.tenants')">{{ t('nav.tenants') }}</ResponsiveNavLink>
                    <ResponsiveNavLink :href="locRoute('about')">{{ t('nav.about') }}</ResponsiveNavLink>
                    <ResponsiveNavLink :href="locRoute('guide')">{{ t('nav.guide') }}</ResponsiveNavLink>
                </div>
                <div class="border-t border-line py-3">
                    <div class="px-4">
                        <div class="text-base font-medium text-ink">{{ $page.props.auth.user.name }}</div>
                        <div class="text-sm text-muted">{{ $page.props.auth.user.email }}</div>
                    </div>
                    <div class="mt-2 space-y-1">
                        <ResponsiveNavLink :href="locRoute('profile.edit')">{{ t('nav.profile') }}</ResponsiveNavLink>
                        <ResponsiveNavLink :href="locRoute('logout')" method="post" as="button">{{ t('nav.logout') }}</ResponsiveNavLink>
                    </div>
                </div>
            </div>
        </nav>
        <p v-if="$page.props.flash?.success" class="border-b border-line bg-surface-2 px-4 py-2 text-center text-sm text-good">{{ $page.props.flash.success }}</p>
        <p v-if="$page.props.flash?.error" class="border-b border-line bg-surface-2 px-4 py-2 text-center text-sm text-danger">{{ $page.props.flash.error }}</p>
        <header v-if="$slots.header" class="border-b border-line/70 bg-surface/40">
            <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>
        <main>
            <slot />
        </main>
    </div>
</template>
