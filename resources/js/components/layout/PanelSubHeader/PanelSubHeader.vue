<script setup lang="ts">
import { cn } from '@/utils/ui';
import type { HTMLAttributes } from 'vue';

const props = defineProps<{ class?: HTMLAttributes['class'] }>();
</script>

<template>
    <div
        :class="
            cn('px-panel h-sub-toolbar flex items-center justify-between', props.class)
        "
    >
        <span class="text-muted-foreground text-xs">
            <slot></slot>
        </span>
        <slot name="toolbox"></slot>
    </div>
</template>
