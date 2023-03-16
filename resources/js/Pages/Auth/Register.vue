<script setup>
import { computed, inject } from 'vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import Footer from '@/Components/Footer.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const translate = inject('translate');

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const termsAndPrivacyPolicyText = computed(() => {
    return translate('I agree to the :terms_of_service and :privacy_policy', {
        terms_of_service: '<a target="_blank" href="' + route('terms.show') + '" class= "underline text-sm text-gray-600 hover:text-gray-900">' + translate('Terms of Service') + '</a>',
        privacy_policy: '<a target="_blank" href="' + route('policy.show') + '" class= "underline text-sm text-gray-600 hover:text-gray-900">' + translate('Privacy Policy') + '</a>'
    });
});

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

    <Head :title="__('Register')" />

    <div class="min-h-screen flex flex-col justify-between bg-gray-cold-15">
        <AuthenticationCard>
            <template #logo>
                <AuthenticationCardLogo />
            </template>

            <p class="text-center text-xl font-semibold text-gray-700 mt-4">
                {{ __('To get started, create a user account') }}
            </p>
            <hr class="my-6 border-gray-300">

            <form @submit.prevent="submit">
                <div>
                    <InputLabel for="name" :value="__('Name')" />
                    <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required autofocus
                        autocomplete="name" />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="mt-4">
                    <InputLabel for="email" :value="__('Email')" />
                    <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password" :value="__('Password')" />
                    <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full" required
                        autocomplete="new-password" />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password_confirmation" :value="__('Confirm Password')" />
                    <TextInput id="password_confirmation" v-model="form.password_confirmation" type="password"
                        class="mt-1 block w-full" required autocomplete="new-password" />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>

                <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                    <InputLabel for="terms">
                        <div class="flex items-center">
                            <Checkbox id="terms" v-model:checked="form.terms" name="terms" required />

                            <div class="ml-2" v-html="termsAndPrivacyPolicyText"></div>
                        </div>
                        <InputError class="mt-2" :message="form.errors.terms" />
                    </InputLabel>
                </div>

                <PrimaryButton class="w-full mt-10" :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing">
                    {{ __('Sign up') }}
                </PrimaryButton>

                <div class="inline-flex items-center justify-center w-full">
                    <hr class="w-64 h-px my-8 bg-gray-300 border-0">
                    <span class="absolute px-3 font-medium text-gray-900 -translate-x-1/2 bg-white left-1/2">
                        {{ __('or') }}
                    </span>
                </div>

                <PrimaryButton @click.stop="google()" class="w-full mt-2 text-white hover:bg-red-900 focus:outline-none focus:ring-4 focus:ring-red-300" :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing">
                    <img src="/images/google.png" class="h-5 w-5 mr-2" />
                    {{ __('Sign up with Google') }}
                </PrimaryButton>

                <PrimaryButton @click.stop="github()" class="w-full mt-2 text-white hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300" :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing">
                    <img src="/images/github-mark-white.png" class="h-5 w-5 mr-2" />
                    {{ __('Sign up with Github') }}
                </PrimaryButton>

                <div class="flex items-center justify-center mt-4">
                    <p class="text-gray-500">{{ __('Already have an account?') }}</p>
                    <Link :href="route('login')" class="ml-1 text-blue-50 hover:underline font-semibold">
                    {{ __('Sign in') }}
                    </Link>
                </div>
            </form>
        </AuthenticationCard>

        <Footer></Footer>
    </div>
</template>
