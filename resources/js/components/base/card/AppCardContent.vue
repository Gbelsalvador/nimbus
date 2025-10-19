<script setup lang="ts">
import { cn } from '@/utils/ui';
import type { HTMLAttributes } from 'vue';

const props = defineProps<{
    class?: HTMLAttributes['class'];
}>();
</script>

<template>
    <div :class="cn('p-4 pt-0', props.class)">
        <slot />
    </div>
</template>
