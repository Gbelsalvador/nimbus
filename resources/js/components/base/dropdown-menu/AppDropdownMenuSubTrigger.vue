<script setup lang="ts">
import { cn } from '@/utils/ui';
import { ChevronRight } from 'lucide-vue-next';
import {
    DropdownMenuSubTrigger,
    type DropdownMenuSubTriggerProps,
    useForwardProps,
} from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';

const props = defineProps<
    DropdownMenuSubTriggerProps & { class?: HTMLAttributes['class'] }
>();

const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props;

    return delegated;
});

const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
    <DropdownMenuSubTrigger
        v-bind="forwardedProps"
        :class="
            cn(
                'flex cursor-default items-center rounded-sm px-2 py-1.5 text-sm outline-none select-none focus:bg-zinc-100 data-[state=open]:bg-zinc-100 dark:focus:bg-zinc-800 dark:data-[state=open]:bg-zinc-800',
                props.class,
            )
        "
    >
        <slot />
        <ChevronRight class="ml-auto h-4 w-4" />
    </DropdownMenuSubTrigger>
</template>
