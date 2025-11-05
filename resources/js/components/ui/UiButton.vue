<script setup>
import { defineProps } from 'vue';

defineProps({
    label: String,
    icon: {
        type: [Object, Function],
        default: null,
    },
});
</script>

<template>
    <Button v-bind="$attrs" :label>
        <template #icon>
            <component :is="icon" class="w-5! h-5!" />
        </template>
    </Button>
</template>