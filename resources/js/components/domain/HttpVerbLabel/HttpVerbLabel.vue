<script setup lang="ts">
import { AppBadge } from '@/components/base/badge';
import { cn } from '@/utils/ui';
import { computed } from 'vue';

interface HttpVerbLabelProps {
    method: string;
    size?: 'sm' | 'md' | 'lg';
    variant?: 'default' | 'outline';
}

const props = withDefaults(defineProps<HttpVerbLabelProps>(), {
    size: 'sm',
    variant: 'outline',
});

const indicatorColor = computed(() => {
    return (
        {
            POST: 'text-emerald-600',
            PUT: 'text-emerald-600',
            DELETE: 'text-rose-500',
        }[props.method] ?? null
    );
});

const sizeClasses = computed(() => {
    return {
        sm: 'min-w-[50px] text-xxs',
        md: 'min-w-[60px] text-xs',
        lg: 'min-w-[70px] text-sm',
    }[props.size];
});
</script>

<template>
    <AppBadge :variant="variant" :class="cn('justify-center', sizeClasses)">
        <span :class="indicatorColor">{{ method }}</span>
    </AppBadge>
</template>
