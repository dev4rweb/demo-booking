<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center justify-center rounded-xl border border-line bg-surface px-4 py-2.5 text-xs font-semibold uppercase tracking-widest text-ink shadow-sm transition duration-200 hover:border-accent hover:text-accent focus:outline-none focus-visible:ring-2 focus-visible:ring-accent disabled:opacity-25"
    >
        <slot />
    </button>
</template>
