<script setup lang="ts">
import { cn } from '@/utils/ui';
import type { HTMLAttributes } from 'vue';

const props = defineProps<{
    class?: HTMLAttributes['class'];
}>();
</script>

<template>
    <ul
        data-sidebar="menu-badge"
        :class="
            cn(
                'border-sidebar-border ml-2.5 flex min-w-0 translate-x-px flex-col gap-1 border-l py-0.5 pl-0.5',
                'group-data-[collapsible=icon]:hidden',
                props.class,
            )
        "
    >
        <slot />
    </ul>
</template>
