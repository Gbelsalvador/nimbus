<script setup lang="ts">
import { cn } from '@/utils/ui';
import type { HTMLAttributes } from 'vue';

const props = defineProps<{
    class?: HTMLAttributes['class'];
}>();
</script>

<template>
    <p :class="cn('text-subtle text-sm', props.class)">
        <slot />
    </p>
</template>
