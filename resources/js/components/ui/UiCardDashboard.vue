<script setup>
import { defineProps } from 'vue';

defineProps({
    title: String,
    subtitle: String,
    content: String,
    footer: String,
    icon: {
        type: [Object, Function],
        default: null,
    },
    classIcon: String,
});
</script>

<template>
    <Card>
        <template #title>
            <h2 class="text-base flex gap-2 items-center justify-between">
                {{ title }}
                <component :is="icon" :class="classIcon" />
            </h2>
        </template>
        <template #subtitle>
            {{ subtitle }}
        </template>
        <template #content>
            <p class="m-0 text-3xl font-bold">
                {{ content }}
            </p>
        </template>
        <template #footer>
            <p class="text-gray-500 text-sm">
                {{ footer }}
            </p>
        </template>
    </Card>
</template>