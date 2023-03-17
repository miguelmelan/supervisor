<script setup>
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Footer from '@/Components/Footer.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const microsoft = () => {
    form.processing = true;
    Inertia.get(route('auth.microsoft.redirect'));
};

const google = () => {
    form.processing = true;
    Inertia.get(route('auth.google.redirect'));
};

const github = () => {
    form.processing = true;
    Inertia.get(route('auth.github.redirect'));
};
</script>

<template>

    <Head :title="__('Log in')" />

    <div class="min-h-screen flex flex-col justify-between bg-gray-cold-15">
        <AuthenticationCard>
            <template #logo>
                <AuthenticationCardLogo />
            </template>

            <p class="text-center text-xl font-semibold text-gray-700 mt-4">{{ __('Sign in to your account') }}</p>
            <hr class="my-6 border-gray-300">

            <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                {{ status }}
            </div>

            <form @submit.prevent="submit">
                <div>
                    <InputLabel for="email" :value="__('Email')" />
                    <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required
                        autofocus />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password" :value="__('Password')" />
                    <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full" required
                        autocomplete="current-password" />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <!-- <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div> -->

                <div class="flex items-center justify-end mt-4">
                    <Link v-if="canResetPassword" :href="route('password.request')"
                        class="text-blue-50 hover:underline">
                    {{ __('Forgot your password?') }}
                    </Link>
                </div>

                <PrimaryButton class="w-full mt-10" :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing">
                    {{ __('Log in') }}
                </PrimaryButton>

                <div class="inline-flex items-center justify-center w-full">
                    <hr class="w-64 h-px my-8 bg-gray-300 border-0">
                    <span class="absolute px-3 font-medium text-gray-900 -translate-x-1/2 bg-white left-1/2">
                        {{ __('or') }}
                    </span>
                </div>

                <PrimaryButton @click.prevent="microsoft()" class="w-full mt-2 text-white hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300" :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing">
                    <img src="/images/microsoft.png" class="h-5 w-5 mr-2" />
                    {{ __('Log in with Microsoft') }}
                </PrimaryButton>

                <PrimaryButton @click.prevent="google()" class="w-full mt-2 text-white hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300" :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing">
                    <img src="/images/google.png" class="h-5 w-5 mr-2" />
                    {{ __('Log in with Google') }}
                </PrimaryButton>

                <PrimaryButton @click.prevent="github()" class="w-full mt-2 text-white hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300" :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing">
                    <img src="/images/github-mark-white.png" class="h-5 w-5 mr-2" />
                    {{ __('Log in with Github') }}
                </PrimaryButton>

                <div class="flex items-center justify-center mt-4">
                    <p class="text-gray-500">{{ __("Don't have an account?") }}</p>
                    <Link :href="route('register')" class="ml-1 text-blue-50 hover:underline font-semibold">
                    {{__('Sign up')}}
                    </Link>
                </div>
            </form>
        </AuthenticationCard>

        <Footer></Footer>
    </div>
</template>
