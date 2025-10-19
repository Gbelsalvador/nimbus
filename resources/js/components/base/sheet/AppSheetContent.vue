<script setup lang="ts">
import { cn } from '@/utils/ui';
import { X } from 'lucide-vue-next';
import {
    DialogClose,
    DialogContent,
    type DialogContentEmits,
    type DialogContentProps,
    DialogOverlay,
    DialogPortal,
    useForwardPropsEmits,
} from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';
import { type SheetVariants, sheetVariants } from './index';

interface SheetContentProps extends DialogContentProps {
    class?: HTMLAttributes['class'];
    side?: SheetVariants['side'];
}

const props = defineProps<SheetContentProps>();

const emits = defineEmits<DialogContentEmits>();

defineOptions({
    inheritAttrs: false,
});

const delegatedProps = computed(() => {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const { class: _, side, ...delegated } = props;

    return delegated;
});

const forwarded = useForwardPropsEmits(delegatedProps, emits);
</script>

<template>
    <DialogPortal>
        <DialogOverlay
            class="data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 fixed inset-0 z-50 bg-black/80"
        />
        <DialogContent
            :class="cn(sheetVariants({ side }), props.class)"
            v-bind="{ ...forwarded, ...$attrs }"
        >
            <slot />

            <DialogClose
                class="absolute top-4 right-4 rounded-sm opacity-70 ring-offset-white transition-opacity hover:opacity-100 focus:ring-2 focus:ring-zinc-950 focus:ring-offset-2 focus:outline-none disabled:pointer-events-none data-[state=open]:bg-zinc-100 dark:ring-offset-zinc-950 dark:focus:ring-zinc-300 dark:data-[state=open]:bg-zinc-800"
            >
                <X class="h-4 w-4" />
            </DialogClose>
        </DialogContent>
    </DialogPortal>
</template>
