<script setup>
import PublicHeader from '@/Components/PublicHeader.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

defineProps({
    githubUrl: String,
    studioUrl: String,
    demoTenants: { type: Array, default: () => [] },
});

const { t, locRoute } = useI18n();

const howSteps = [
    { title: 'about.how_1_title', body: 'about.how_1_body' },
    { title: 'about.how_2_title', body: 'about.how_2_body' },
    { title: 'about.how_3_title', body: 'about.how_3_body' },
];

const uses = [
    { title: 'about.use_salon_title', body: 'about.use_salon_body' },
    { title: 'about.use_fitness_title', body: 'about.use_fitness_body' },
    { title: 'about.use_tutor_title', body: 'about.use_tutor_body' },
    { title: 'about.use_auto_title', body: 'about.use_auto_body' },
    { title: 'about.use_clinic_title', body: 'about.use_clinic_body' },
    { title: 'about.use_photo_title', body: 'about.use_photo_body' },
];

const roles = [
    {
        title: 'about.role_guest_title',
        items: ['about.role_guest_1', 'about.role_guest_2', 'about.role_guest_3', 'about.role_guest_4'],
    },
    {
        title: 'about.role_staff_title',
        items: ['about.role_staff_1', 'about.role_staff_2', 'about.role_staff_3'],
    },
    {
        title: 'about.role_owner_title',
        items: [
            'about.role_owner_1',
            'about.role_owner_2',
            'about.role_owner_3',
            'about.role_owner_4',
            'about.role_owner_5',
            'about.role_owner_6',
        ],
    },
    {
        title: 'about.role_admin_title',
        items: ['about.role_admin_1', 'about.role_admin_2'],
    },
];

const stack = ['Laravel 12', 'Vue 3', 'Inertia', 'Tailwind'];
</script>

<template>
    <SeoHead />

    <div class="min-h-screen">
        <PublicHeader />

        <main class="mx-auto max-w-6xl px-4 pb-20 sm:px-6">
            <section class="mx-auto max-w-3xl py-8 text-center sm:py-14">
                <p class="mb-3 text-xs font-semibold uppercase tracking-[0.22em] text-accent">{{ t('about.kicker') }}</p>
                <h1 class="text-balance text-3xl font-semibold tracking-tight text-ink sm:text-5xl">{{ t('about.title') }}</h1>
                <p class="mx-auto mt-4 max-w-2xl text-pretty text-base leading-7 text-muted sm:text-lg">{{ t('about.lead') }}</p>
            </section>

            <section class="mx-auto grid max-w-3xl gap-3 sm:grid-cols-2" aria-labelledby="about-what">
                <div class="sm:col-span-2">
                    <h2 id="about-what" class="text-xl font-semibold text-ink sm:text-2xl">{{ t('about.what_title') }}</h2>
                </div>
                <article class="surface-card p-5">
                    <p class="text-sm leading-7 text-ink">{{ t('about.what_1') }}</p>
                </article>
                <article class="surface-card p-5">
                    <p class="text-sm leading-7 text-ink">{{ t('about.what_2') }}</p>
                </article>
                <article class="surface-card p-5">
                    <p class="text-sm leading-7 text-ink">{{ t('about.what_3') }}</p>
                </article>
                <article class="surface-card p-5">
                    <p class="text-sm leading-7 text-ink">{{ t('about.what_4') }}</p>
                </article>
            </section>

            <section class="mx-auto mt-12 max-w-3xl" aria-labelledby="about-how">
                <h2 id="about-how" class="text-xl font-semibold text-ink sm:text-2xl">{{ t('about.how_title') }}</h2>
                <ol class="mt-5 grid gap-3 sm:grid-cols-3">
                    <li v-for="(step, index) in howSteps" :key="step.title" class="surface-card p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-accent">0{{ index + 1 }}</p>
                        <h3 class="mt-2 text-base font-semibold text-ink">{{ t(step.title) }}</h3>
                        <p class="mt-2 text-sm leading-6 text-muted">{{ t(step.body) }}</p>
                    </li>
                </ol>
            </section>

            <section class="mx-auto mt-12 max-w-3xl" aria-labelledby="about-use">
                <h2 id="about-use" class="text-xl font-semibold text-ink sm:text-2xl">{{ t('about.use_title') }}</h2>
                <p class="mt-2 text-sm text-muted sm:text-base">{{ t('about.use_subtitle') }}</p>
                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    <article v-for="item in uses" :key="item.title" class="soft-ring surface-card p-5">
                        <h3 class="text-base font-semibold text-ink">{{ t(item.title) }}</h3>
                        <p class="mt-2 text-sm leading-6 text-muted">{{ t(item.body) }}</p>
                    </article>
                </div>
            </section>

            <section class="mx-auto mt-12 max-w-3xl" aria-labelledby="about-roles">
                <h2 id="about-roles" class="text-xl font-semibold text-ink sm:text-2xl">{{ t('about.roles_title') }}</h2>
                <div class="mt-5 grid gap-3 lg:grid-cols-2">
                    <article v-for="role in roles" :key="role.title" class="surface-card p-5 sm:p-6">
                        <h3 class="text-base font-semibold text-ink">{{ t(role.title) }}</h3>
                        <ul class="mt-3 space-y-2 text-sm leading-6 text-muted">
                            <li v-for="item in role.items" :key="item" class="flex gap-2">
                                <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-accent" aria-hidden="true" />
                                <span>{{ t(item) }}</span>
                            </li>
                        </ul>
                    </article>
                </div>
            </section>

            <section class="mx-auto mt-12 grid max-w-3xl gap-3 sm:grid-cols-2" aria-labelledby="about-auto">
                <h2 id="about-auto" class="text-xl font-semibold text-ink sm:col-span-2 sm:text-2xl">{{ t('about.auto_title') }}</h2>
                <article class="surface-card p-5 sm:p-6">
                    <p class="text-sm leading-7 text-ink">{{ t('about.auto_reminders') }}</p>
                </article>
                <article class="surface-card p-5 sm:p-6">
                    <p class="text-sm leading-7 text-ink">{{ t('about.auto_messages') }}</p>
                </article>
            </section>

            <section class="mx-auto mt-12 max-w-3xl" aria-labelledby="about-demo">
                <div class="surface-card p-5 sm:p-8">
                    <h2 id="about-demo" class="text-xl font-semibold text-ink sm:text-2xl">{{ t('about.demo_title') }}</h2>
                    <p class="mt-3 text-sm leading-7 text-muted">{{ t('about.demo_body') }}</p>
                    <div v-if="demoTenants.length" class="mt-5 grid gap-3 sm:grid-cols-2">
                        <Link
                            v-for="studio in demoTenants"
                            :key="studio.slug"
                            :href="locRoute('booking.show', { slug: studio.slug })"
                            class="rounded-2xl border border-line bg-surface-2/70 px-4 py-3 text-sm transition hover:border-accent"
                        >
                            <span class="font-medium text-ink">{{ studio.name }}</span>
                            <span class="ms-2 text-muted">/b/{{ studio.slug }}</span>
                        </Link>
                    </div>
                    <aside class="mt-5 rounded-2xl border border-line bg-surface-2/70 px-4 py-4 text-sm text-muted">
                        <p class="mb-2 font-medium text-ink">{{ t('welcome.demo_credentials') }}</p>
                        <p>{{ t('welcome.owner') }}: owner@demo.local / demo123456</p>
                        <p>{{ t('welcome.staff') }}: staff@demo.local / demo123456</p>
                        <p>{{ t('welcome.admin') }}: admin@demo.local / demo123456</p>
                    </aside>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <Link :href="demoTenants[0] ? locRoute('booking.show', { slug: demoTenants[0].slug }) : locRoute('welcome')" class="inline-flex rounded-xl bg-accent px-4 py-2.5 text-sm font-semibold text-accent-fg">
                            {{ t('about.cta_book') }}
                        </Link>
                        <Link :href="locRoute('guide')" class="inline-flex rounded-xl border border-line px-4 py-2.5 text-sm font-semibold text-ink transition hover:border-accent">
                            {{ t('about.cta_guide') }}
                        </Link>
                        <Link :href="locRoute('login')" class="inline-flex rounded-xl border border-line px-4 py-2.5 text-sm font-semibold text-ink transition hover:border-accent">
                            {{ t('about.cta_login') }}
                        </Link>
                    </div>
                </div>
            </section>

            <footer class="mx-auto mt-10 max-w-3xl text-center">
                <p class="text-sm leading-7 text-muted">
                    {{ t('about.author_before') }}
                    <a :href="studioUrl" class="font-medium text-accent underline-offset-2 hover:underline" target="_blank" rel="noopener">{{ t('about.author_name') }}</a>
                    {{ t('about.author_after') }}
                </p>
                <p class="mt-4 text-[11px] uppercase tracking-[0.16em] text-muted">{{ t('about.stack_note') }}</p>
                <div class="mt-3 flex flex-wrap justify-center gap-2">
                    <span v-for="item in stack" :key="item" class="rounded-full border border-line px-2.5 py-1 text-[11px] text-muted">{{ item }}</span>
                </div>
                <p class="mt-4 text-xs text-muted">
                    GitHub:
                    <a :href="githubUrl" class="text-accent underline-offset-2 hover:underline" target="_blank" rel="noopener">dev4rweb/demo-booking</a>
                </p>
            </footer>
        </main>
    </div>
</template>
