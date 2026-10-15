<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

const props = defineProps({
    status: {
        type: String,
    },
});

const { t, locRoute } = useI18n();
const form = useForm({});

const submit = () => {
    form.post(locRoute('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head :title="t('auth.verify_title')" />

        <div class="mb-4 text-sm text-muted">
            {{ t('auth.verify_body') }}
        </div>

        <div v-if="verificationLinkSent" class="mb-4 text-sm font-medium text-good">
            {{ t('auth.verify_sent') }}
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between gap-3">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ t('auth.resend') }}
                </PrimaryButton>
                <Link :href="locRoute('logout')" method="post" as="button" class="text-sm text-muted underline hover:text-ink">
                    {{ t('nav.logout') }}
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
