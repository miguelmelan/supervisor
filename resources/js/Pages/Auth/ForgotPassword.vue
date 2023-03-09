<script setup>
import { Head, useForm } from '@inertiajs/inertia-vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Footer from '@/Components/Footer.vue';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>

    <Head :title="__('Reset Password')" />

    <div class="min-h-screen flex flex-col justify-between bg-gray-cold-15">
        <AuthenticationCard>
            <template #logo>
                <AuthenticationCardLogo />
            </template>

            <div class="text-center text-xl font-semibold text-gray-700 mt-4">
                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </div>
            <hr class="my-6 border-gray-300">

            <div v-if="status" class="my-4 font-medium text-sm text-green-600">
                {{ status }}
            </div>

            <form @submit.prevent="submit">
                <div>
                    <InputLabel for="email" value="Email" />
                    <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required
                        autofocus />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        {{ __('Email Password Reset Link') }}
                    </PrimaryButton>
                </div>
            </form>
        </AuthenticationCard>

        <Footer></Footer>
    </div>
</template>
