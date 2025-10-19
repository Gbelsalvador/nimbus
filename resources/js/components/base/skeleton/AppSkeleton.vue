<script setup lang="ts">
import { cn } from '@/utils/ui';
import type { HTMLAttributes } from 'vue';

interface SkeletonProps {
    class?: HTMLAttributes['class'];
}

const props = defineProps<SkeletonProps>();
</script>

<template>
    <div
        :class="
            cn('animate-pulse rounded-md bg-zinc-900/10 dark:bg-zinc-50/10', props.class)
        "
    />
</template>
