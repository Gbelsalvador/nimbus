<script setup lang="ts">
import { cn } from '@/utils/ui';
import type { HTMLAttributes } from 'vue';
import { type BadgeVariants, badgeVariants } from './index';

type BadgeInterface = {
    variant?: BadgeVariants['variant'];
    class?: HTMLAttributes['class'];
};

const props = defineProps<BadgeInterface>();
</script>

<template>
    <div :class="cn(badgeVariants({ variant }), props.class)">
        <slot />
    </div>
</template>
