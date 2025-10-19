<script setup lang="ts">
import { cn } from '@/utils/ui';
import type { HTMLAttributes } from 'vue';

const props = defineProps<{
    class?: HTMLAttributes['class'];
}>();
</script>

<template>
    <caption :class="cn('text-subtle mt-4 text-sm', props.class)">
        <slot />
    </caption>
</template>
