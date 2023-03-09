<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Footer from '@/Components/Footer.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    status: String,
});

const form = useForm();

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>

    <Head :title="__('Email Verification')" />

    <div class="min-h-screen flex flex-col justify-between bg-gray-cold-15">

        <AuthenticationCard>
            <template #logo>
                <AuthenticationCardLogo />
            </template>
            <hr class="my-6 border-gray-300">

            <div class="mb-4 text-sm text-gray-600">
                {{ __("Before continuing, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.") }}
            </div>

            <div v-if="verificationLinkSent" class="mb-4 font-medium text-sm text-green-600">
                {{ __("A new verification link has been sent to the email address you provided in your profile settings.") }}
            </div>

            <form @submit.prevent="submit">
                <div class="mt-4 flex items-center justify-between">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        {{ __("Resend Verification Email") }}
                    </PrimaryButton>

                    <div class="text-right">
                        <Link :href="route('profile.show')" class="underline text-sm text-gray-600 hover:text-gray-900">
                        {{ __("Edit Profile") }}
                        </Link>

                        <Link :href="route('logout')" method="post" as="button"
                            class="underline text-sm text-gray-600 hover:text-gray-900 ml-2">
                        {{ __("Log Out") }}
                        </Link>
                    </div>
                </div>
            </form>
        </AuthenticationCard>

        <Footer></Footer>
    </div>
</template>
