<template>
    <div class="flex flex-grow flex-col sm:justify-center items-center sm:pt-0">
        <div class="w-full sm:max-w-lg sm:my-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="mx-6 mt-4">
                <slot name="logo" />
            </div>
            <slot />
        </div>
    </div>
</template>
