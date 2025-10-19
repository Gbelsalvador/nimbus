<script setup lang="ts">
import { AppInput } from '@/components/base/input';
import { cn } from '@/utils/ui';
import type { HTMLAttributes } from 'vue';

const props = defineProps<{
    class?: HTMLAttributes['class'];
}>();
</script>

<template>
    <AppInput
        data-sidebar="input"
        :class="
            cn(
                'focus-visible:ring-sidebar-ring h-8 w-full bg-white shadow-none focus-visible:ring-2 dark:bg-zinc-950',
                props.class,
            )
        "
    >
        <slot />
    </AppInput>
</template>
