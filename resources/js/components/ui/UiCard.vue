<script setup>
import { defineProps } from 'vue';

defineProps({
    title: String,
    value: String | Number,
    icon: {
        type: [Object, Function],
        default: null,
    },
    colorIcon: String,
    colorIconBackground: String,
});
</script>

<template>
    <Card>
        <template #content>
            <div class="flex p-2 items-center">
                <div :class="`p-4 rounded-md mr-4 ${colorIconBackground}`">
                    <component :is="icon" :class="`w-8! h-8! ${colorIcon}`" />
                </div>
                <div>
                    <p class="mb-2">{{ title }}</p>
                    <p class="font-bold text-2xl">{{ value }}</p>
                </div>
            </div>
        </template>
    </Card>
</template>