<script setup>
import PublicHeader from '@/Components/PublicHeader.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

defineProps({
    githubUrl: String,
    demoTenants: { type: Array, default: () => [] },
});

const { t, locRoute } = useI18n();
const stack = ['Laravel 12', 'Vue 3', 'Inertia.js', 'Tailwind CSS', 'Cashier / Stripe', 'Telegram', 'MySQL'];
</script>

<template>
    <SeoHead />

    <div class="min-h-screen">
        <PublicHeader />

        <main class="mx-auto max-w-6xl px-4 pb-16 sm:px-6">
            <section class="mx-auto max-w-3xl py-8 text-center sm:py-14">
                <p class="mb-3 text-xs font-semibold uppercase tracking-[0.22em] text-accent">{{ t('welcome.kicker') }}</p>
                <h1 class="text-balance text-4xl font-semibold tracking-tight text-ink sm:text-5xl">{{ t('welcome.title') }}</h1>
                <p class="mx-auto mt-4 max-w-2xl text-pretty text-base text-muted sm:text-lg">{{ t('welcome.tagline') }}</p>
            </section>

            <section class="surface-card mx-auto max-w-3xl p-5 sm:p-8">
                <h2 class="text-xl font-semibold text-ink">{{ t('welcome.about_title') }}</h2>
                <p class="mt-3 text-sm leading-7 text-muted sm:text-base">{{ t('welcome.about_body') }}</p>
                <p class="mt-4 text-sm text-muted">{{ t('welcome.author') }}</p>
                <div class="mt-6 border-t border-line pt-6">
                    <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-muted">{{ t('welcome.stack_title') }}</h3>
                    <div class="flex flex-wrap gap-2">
                        <span v-for="item in stack" :key="item" class="rounded-full border border-line bg-surface-2 px-3 py-1 text-xs font-medium text-ink">{{ item }}</span>
                    </div>
                </div>
            </section>

            <section class="mx-auto mt-6 grid max-w-3xl gap-4 sm:grid-cols-2">
                <Link :href="locRoute('about')" class="soft-ring surface-card block p-6 text-center sm:col-span-2">
                    <h3 class="text-xl font-semibold text-ink">{{ t('welcome.product') }}</h3>
                    <p class="mt-2 text-sm text-muted">{{ t('welcome.product_hint') }}</p>
                </Link>
                <Link :href="locRoute('login')" class="soft-ring surface-card block p-6 text-center">
                    <h3 class="text-xl font-semibold text-ink">{{ t('welcome.owner_panel') }}</h3>
                    <p class="mt-2 text-sm text-muted">{{ t('welcome.owner_panel_hint') }}</p>
                </Link>
                <Link
                    :href="demoTenants[0] ? locRoute('booking.show', { slug: demoTenants[0].slug }) : locRoute('guide')"
                    class="soft-ring surface-card block p-6 text-center"
                >
                    <h3 class="text-xl font-semibold text-ink">{{ t('welcome.try_booking') }}</h3>
                    <p class="mt-2 text-sm text-muted">{{ t('welcome.try_booking_hint') }}</p>
                </Link>
                <Link :href="locRoute('guide')" class="soft-ring surface-card block p-6 text-center sm:col-span-2">
                    <h3 class="text-xl font-semibold text-ink">{{ t('welcome.how_to') }}</h3>
                    <p class="mt-2 text-sm text-muted">{{ t('welcome.how_to_hint') }}</p>
                </Link>
            </section>

            <section v-if="demoTenants.length" class="mx-auto mt-6 grid max-w-3xl gap-3 sm:grid-cols-2">
                <Link
                    v-for="studio in demoTenants"
                    :key="studio.slug"
                    :href="locRoute('booking.show', { slug: studio.slug })"
                    class="rounded-2xl border border-line bg-surface/70 px-4 py-3 text-sm transition hover:border-accent"
                >
                    <span class="font-medium text-ink">{{ studio.name }}</span>
                    <span class="ms-2 text-muted">/b/{{ studio.slug }}</span>
                </Link>
            </section>

            <aside class="mx-auto mt-6 max-w-3xl rounded-2xl border border-line bg-surface/70 px-5 py-4 text-center text-sm text-muted">
                <p class="mb-2 font-medium text-ink">{{ t('welcome.demo_credentials') }}</p>
                <p>{{ t('welcome.owner') }}: owner@demo.local / demo123456</p>
                <p>{{ t('welcome.staff') }}: staff@demo.local / demo123456</p>
                <p>{{ t('welcome.admin') }}: admin@demo.local / demo123456</p>
                <p class="mt-3">
                    GitHub:
                    <a :href="githubUrl" class="text-accent underline-offset-2 hover:underline" target="_blank">dev4rweb/demo-booking</a>
                </p>
            </aside>
        </main>
    </div>
</template>
