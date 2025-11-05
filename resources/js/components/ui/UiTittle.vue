<script setup>
import { defineProps } from 'vue';

defineProps({
    text: String,
    icon: {
        type: [Object, Function],
        default: null,
    },
});
</script>

<template>
    <h1 class="flex gap-2 items-center text-4xl font-bold">
        {{ text }}
        <component :is="icon" class="w-4 h-4" />
    </h1>
</template>