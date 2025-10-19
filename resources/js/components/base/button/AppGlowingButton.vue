<script setup lang="ts">
import { Props } from '@/components/base/button/AppButton.vue';
import { AppButton } from '@/components/base/button/index';
import AppBorderBeam from '@/components/base/glow-border/AppBorderBeam.vue';
import { cn } from '@/utils/ui';

interface AppGlowingButtonProps extends Props {
    duration?: number;
    beamSize?: number;
}

const props = withDefaults(defineProps<AppGlowingButtonProps>(), {
    size: 'xs',
    variant: 'secondary',
    duration: 10,
    beamSize: 200,
});
</script>

<template>
    <AppButton :size="size" :variant="variant" :class="cn('group relative', props.class)">
        <AppBorderBeam
            :size="beamSize"
            :duration="duration"
            :delay="0"
            :border-width="1"
            class="group-disabled:hidden"
        />
        <slot></slot>
    </AppButton>
</template>
