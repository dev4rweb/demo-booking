<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center justify-center rounded-xl border border-transparent bg-accent px-4 py-2.5 text-xs font-semibold uppercase tracking-widest text-accent-fg transition duration-200 ease-out hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent focus-visible:ring-offset-2 focus-visible:ring-offset-canvas active:translate-y-px disabled:opacity-50"
    >
        <slot />
    </button>
</template>
