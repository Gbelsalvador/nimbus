<script setup lang="ts">
import { cn } from '@/utils/ui';
import type { HTMLAttributes } from 'vue';

const props = defineProps<{
    class?: HTMLAttributes['class'];
}>();
</script>

<template>
    <main
        :class="
            cn(
                'relative flex min-h-svh flex-1 flex-col bg-white dark:bg-zinc-950',
                'peer-data-[variant=inset]:min-h-[calc(100svh-theme(spacing.4))] md:peer-data-[variant=inset]:m-2 md:peer-data-[variant=inset]:ml-0 md:peer-data-[variant=inset]:rounded-xl md:peer-data-[variant=inset]:shadow md:peer-data-[state=collapsed]:peer-data-[variant=inset]:ml-2',
                props.class,
            )
        "
    >
        <slot />
    </main>
</template>
