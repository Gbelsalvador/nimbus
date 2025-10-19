<script setup lang="ts">
import { cn } from '@/utils/ui';
import type { HTMLAttributes } from 'vue';

const props = defineProps<{
    class?: HTMLAttributes['class'];
}>();
</script>

<template>
    <tfoot
        :class="
            cn(
                'border-t bg-zinc-100/50 font-medium dark:bg-zinc-800/50 [&>tr]:last:border-b-0',
                props.class,
            )
        "
    >
        <slot />
    </tfoot>
</template>
