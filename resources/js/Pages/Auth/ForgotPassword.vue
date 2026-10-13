<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

defineProps({
    status: {
        type: String,
    },
});

const { t, locRoute } = useI18n();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(locRoute('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head :title="t('auth.forgot_title')" />

        <div class="mb-4 text-sm text-muted">
            {{ t('auth.forgot_body') }}
        </div>

        <div v-if="status" class="mb-4 text-sm font-medium text-good">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" :value="t('common.email')" />
                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ t('auth.email_link') }}
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
