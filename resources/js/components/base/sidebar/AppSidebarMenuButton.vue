<script setup lang="ts">
import {
    AppTooltip,
    AppTooltipContent,
    AppTooltipTrigger,
} from '@/components/base/tooltip';
import { type Component, computed } from 'vue';
import SidebarMenuButtonChild, {
    type SidebarMenuButtonProps,
} from './AppSidebarMenuButtonChild.vue';
import { useSidebar } from './utils';

const props = withDefaults(
    defineProps<
        SidebarMenuButtonProps & {
            tooltip?: string | Component;
        }
    >(),
    {
        as: 'button',
        variant: 'default',
        size: 'default',
        tooltip: '',
    },
);

defineOptions({
    inheritAttrs: false,
});

const { state } = useSidebar();

const delegatedProps = computed(() => {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const { tooltip, ...delegated } = props;

    return delegated;
});
</script>

<template>
    <SidebarMenuButtonChild v-if="!tooltip" v-bind="{ ...delegatedProps, ...$attrs }">
        <slot />
    </SidebarMenuButtonChild>

    <AppTooltip v-else>
        <AppTooltipTrigger as-child>
            <SidebarMenuButtonChild v-bind="{ ...delegatedProps, ...$attrs }">
                <slot />
            </SidebarMenuButtonChild>
        </AppTooltipTrigger>
        <AppTooltipContent side="right" align="center" :hidden="state !== 'collapsed'">
            <template v-if="typeof tooltip === 'string'">
                {{ tooltip }}
            </template>
            <component :is="tooltip" v-else />
        </AppTooltipContent>
    </AppTooltip>
</template>
