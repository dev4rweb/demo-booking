<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from '@/composables/useI18n';

const { t, locRoute } = useI18n();
const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(locRoute('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-ink">{{ t('profile.password') }}</h2>
            <p class="mt-1 text-sm text-muted">{{ t('profile.password_help') }}</p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <div>
                <InputLabel for="current_password" :value="t('profile.current')" />
                <TextInput id="current_password" ref="currentPasswordInput" v-model="form.current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
                <InputError :message="form.errors.current_password" class="mt-2" />
            </div>
            <div>
                <InputLabel for="password" :value="t('profile.new')" />
                <TextInput id="password" ref="passwordInput" v-model="form.password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                <InputError :message="form.errors.password" class="mt-2" />
            </div>
            <div>
                <InputLabel for="password_confirmation" :value="t('profile.confirm')" />
                <TextInput id="password_confirmation" v-model="form.password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                <InputError :message="form.errors.password_confirmation" class="mt-2" />
            </div>
            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">{{ t('common.save') }}</PrimaryButton>
                <p v-if="form.recentlySuccessful" class="text-sm text-muted">{{ t('common.saved') }}</p>
            </div>
        </form>
    </section>
</template>
