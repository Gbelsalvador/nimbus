<script setup lang="ts">
import { TriangleAlertIcon } from 'lucide-vue-next';

interface ErrorCardHeaderProps {
    message: string;
}

const props = defineProps<ErrorCardHeaderProps>();
</script>

<template>
    <div
        class="relative z-10 border-b bg-gradient-to-r from-red-50/10 to-transparent dark:from-red-950/30"
    >
        <div class="flex gap-3">
            <div class="flex items-center bg-red-50 px-6 py-3 dark:bg-red-950">
                <TriangleAlertIcon class="size-6 text-red-600 dark:text-white" />
            </div>
            <div class="p-3">
                <h2 class="text-md leading-tight font-semibold">
                    {{ props.message }}
                </h2>
                <p class="text-subtle-foreground text-sm">Route extraction failed</p>
            </div>
        </div>
    </div>
</template>
