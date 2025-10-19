<script setup lang="ts">
import AppRoundIndicator from '@/components/base/round-indicator/AppRoundIndicator.vue';
import { computed } from 'vue';

/*
 * Constants.
 */

const STATUS_COLORS = {
    success: 'text-emerald-600',
    error: 'text-rose-500',
    neutral: 'text-zinc-500',
} as const;

const STATUS_MESSAGES = {
    allGood: 'All good',
    issuesFound: 'Issues found',
    noRoutes: 'No routes',
} as const;

/*
 * Props.
 */

interface Props {
    routesWithErrors: number;
    totalRoutes: number;
}

const props = defineProps<Props>();

/*
 * Computed Properties.
 */

const statusIndicator = computed(() => {
    const { routesWithErrors, totalRoutes } = props;

    if (routesWithErrors === 0 && totalRoutes > 0) {
        return {
            color: STATUS_COLORS.success,
            message: STATUS_MESSAGES.allGood,
        };
    }

    if (routesWithErrors > 0) {
        return {
            color: STATUS_COLORS.error,
            message: STATUS_MESSAGES.issuesFound,
        };
    }

    return { color: STATUS_COLORS.neutral, message: STATUS_MESSAGES.noRoutes };
});
</script>

<template>
    <div class="flex items-center space-x-2">
        <AppRoundIndicator :class="statusIndicator.color" />
        <span class="text-muted-foreground text-xs">
            {{ statusIndicator.message }}
        </span>
    </div>
</template>
