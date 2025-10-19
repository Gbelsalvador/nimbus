<script setup lang="ts">
import AppPanelRipple from '@/components/base/AppPanelRipple.vue';
import { cn } from '@/utils/ui';
import type { HTMLAttributes } from 'vue';

interface Props {
    class?: HTMLAttributes['class'];
}

const props = defineProps<Props>();
</script>

<template>
    <div :class="cn('relative h-full flex-1 overflow-hidden p-2', props.class)">
        <div class="max-w-md">
            <slot />
        </div>
        <AppPanelRipple />
    </div>
</template>
