<script setup lang="ts">
import { cn } from '@/utils/ui';
import type { HTMLAttributes } from 'vue';

const props = defineProps<{
    class?: HTMLAttributes['class'];
}>();
</script>

<template>
    <thead :class="cn('[&_tr]:border-b', props.class)">
        <slot />
    </thead>
</template>
