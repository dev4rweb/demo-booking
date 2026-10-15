<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

const { t, locRoute } = useI18n();

const form = useForm({
    name: '',
    business: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(locRoute('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head :title="t('auth.register')" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" :value="t('auth.name')" />
                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus autocomplete="name" />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <div class="mt-4">
                <InputLabel for="business" :value="t('auth.business')" />
                <TextInput id="business" type="text" class="mt-1 block w-full" v-model="form.business" autocomplete="organization" />
                <InputError class="mt-2" :message="form.errors.business" />
            </div>
            <div class="mt-4">
                <InputLabel for="email" :value="t('common.email')" />
                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div class="mt-4">
                <InputLabel for="password" :value="t('auth.password')" />
                <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>
            <div class="mt-4">
                <InputLabel for="password_confirmation" :value="t('auth.confirm_password')" />
                <TextInput id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" required autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>
            <div class="mt-4 flex items-center justify-end gap-3">
                <Link :href="locRoute('login')" class="text-sm text-muted underline hover:text-ink">
                    {{ t('auth.already') }}
                </Link>
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ t('auth.register') }}
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
