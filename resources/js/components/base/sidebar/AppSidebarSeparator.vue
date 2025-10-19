<script setup lang="ts">
import { AppSeparator } from '@/components/base/separator';
import { cn } from '@/utils/ui';
import type { HTMLAttributes } from 'vue';

const props = defineProps<{
    class?: HTMLAttributes['class'];
}>();
</script>

<template>
    <AppSeparator
        data-sidebar="separator"
        :class="cn('bg-sidebar-border mx-2 w-auto', props.class)"
    >
        <slot />
    </AppSeparator>
</template>
