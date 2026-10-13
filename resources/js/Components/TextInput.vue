<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="rounded-xl border-line bg-surface text-ink shadow-sm placeholder:text-muted/70 focus:border-accent focus:ring-accent"
        v-model="model"
        ref="input"
    />
</template>
