<script setup>
const props = defineProps({
   loading: Boolean,
   message: String
});
</script>
<template>
    <div
        v-if="loading"
        class="fixed inset-0 w-full h-full flex items-center justify-center z-10000"
    >
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="relative rounded-lg p-6 flex flex-col items-center gap-4 w-80">
            <ProgressSpinner
                style="width: 50px; height: 50px"
                strokeWidth="6"
                fill="transparent"
                animationDuration=".8s"
            />
            <span class="text-white font-medium">{{ message }}</span>
        </div>
    </div>
</template>