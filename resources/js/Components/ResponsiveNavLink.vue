<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full border-s-4 border-accent bg-surface-2 px-4 py-2 text-start text-base font-medium text-ink'
        : 'block w-full border-s-4 border-transparent px-4 py-2 text-start text-base font-medium text-muted transition hover:border-line hover:bg-surface-2 hover:text-ink',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
