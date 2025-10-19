<script setup lang="ts">
import {
    AppDialog,
    AppDialogContent,
    AppDialogDescription,
    AppDialogHeader,
    AppDialogTitle,
} from '@/components/base/dialog';
import type { DialogRootEmits, DialogRootProps } from 'reka-ui';
import { useForwardPropsEmits } from 'reka-ui';
import AppCommand from './AppCommand.vue';

const props = withDefaults(
    defineProps<
        DialogRootProps & {
            title?: string;
            description?: string;
        }
    >(),
    {
        title: 'Command Palette',
        description: 'Search for a command to run...',
    },
);
const emits = defineEmits<DialogRootEmits>();

const forwarded = useForwardPropsEmits(props, emits);
</script>

<template>
    <AppDialog v-bind="forwarded">
        <AppDialogContent class="overflow-hidden p-0">
            <AppDialogHeader class="sr-only">
                <AppDialogTitle>{{ title }}</AppDialogTitle>
                <AppDialogDescription>{{ description }}</AppDialogDescription>
            </AppDialogHeader>
            <AppCommand>
                <slot />
            </AppCommand>
        </AppDialogContent>
    </AppDialog>
</template>
