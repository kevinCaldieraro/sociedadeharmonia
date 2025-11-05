<script setup>
import ApplicationLogo from '@/components/ApplicationLogo.vue';
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
</script>

<template>
    <Button
        :href="route('home')"
        pt:root:class="flex items-center justify-start gap-4 no-underline p-0"
        variant="link"
        as="InertiaLink"
    >
        <ApplicationLogo class="block w-12 fill-current text-surface-900 dark:text-surface-0" />
        <span class="font-bold">{{ appName }}</span>
    </Button>
</template>