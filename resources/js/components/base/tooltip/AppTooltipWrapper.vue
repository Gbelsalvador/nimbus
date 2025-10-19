<script setup lang="ts">
import {
    AppTooltip,
    AppTooltipContent,
    AppTooltipProvider,
    AppTooltipTrigger,
} from '@/components/base/tooltip/index';
import { AsTag } from 'reka-ui';
import { Component } from 'vue';

interface PropsShape {
    value: string;
    delay?: number;
    as?: AsTag | Component;
    onClick?: () => void;
}

const props = withDefaults(defineProps<PropsShape>(), {
    as: 'button',
    delay: 300,
    onClick: () => {},
});
</script>

<template>
    <AppTooltipProvider :delay-duration="delay">
        <AppTooltip>
            <AppTooltipTrigger :as="as" @click="onClick">
                <span><slot /></span>
            </AppTooltipTrigger>
            <AppTooltipContent>{{ props.value }}</AppTooltipContent>
        </AppTooltip>
    </AppTooltipProvider>
</template>
