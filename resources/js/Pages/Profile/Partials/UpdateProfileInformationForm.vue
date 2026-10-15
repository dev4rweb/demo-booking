<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;
const { t, locRoute } = useI18n();

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-ink">{{ t('profile.info') }}</h2>
            <p class="mt-1 text-sm text-muted">{{ t('profile.info_help') }}</p>
        </header>

        <form @submit.prevent="form.patch(locRoute('profile.update'))" class="mt-6 space-y-6">
            <div>
                <InputLabel for="name" :value="t('common.name')" />
                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus autocomplete="name" />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <div>
                <InputLabel for="email" :value="t('common.email')" />
                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-ink">
                    {{ t('profile.unverified') }}
                    <Link :href="locRoute('verification.send')" method="post" as="button" class="text-sm text-muted underline hover:text-ink">
                        {{ t('profile.resend') }}
                    </Link>
                </p>
                <div v-show="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-good">
                    {{ t('profile.resent') }}
                </div>
            </div>
            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">{{ t('common.save') }}</PrimaryButton>
                <p v-if="form.recentlySuccessful" class="text-sm text-muted">{{ t('common.saved') }}</p>
            </div>
        </form>
    </section>
</template>
