<script setup>
// import AppLayout from '@/layouts/app/HeaderLayout.vue';
import AppLayout from '@/layouts/app/SidebarLayout.vue';

const props = defineProps({
    breadcrumbs: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <AppLayout :breadcrumbs="props.breadcrumbs">
        <slot />
    </AppLayout>
</template>
