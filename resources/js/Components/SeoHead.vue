<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const title = computed(() => page.props.seo?.title || '');
const description = computed(() => page.props.seo?.description || '');
const url = computed(() => (typeof window === 'undefined' ? '' : window.location.href));
const image = 'https://demo-booking.dev4rweb.com/og-image.jpg';
</script>

<template>
    <Head :title="title">
        <meta head-key="description" name="description" :content="description" />
        <meta head-key="og:title" property="og:title" :content="title" />
        <meta head-key="og:description" property="og:description" :content="description" />
        <meta head-key="og:image" property="og:image" :content="image" />
        <meta head-key="og:url" property="og:url" :content="url" />
        <meta head-key="og:type" property="og:type" content="website" />
        <meta head-key="twitter:card" name="twitter:card" content="summary_large_image" />
        <meta head-key="twitter:title" name="twitter:title" :content="title" />
        <meta head-key="twitter:description" name="twitter:description" :content="description" />
        <meta head-key="twitter:image" name="twitter:image" :content="image" />
    </Head>
</template>
