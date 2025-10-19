<script setup lang="ts">
import { AppButton } from '@/components/base/button';
import { CheckCircleIcon, RouteIcon } from 'lucide-vue-next';

/*
 * Props.
 */

interface Props {
    totalRoutes: number;
}

defineProps<Props>();

/*
 * Emits.
 */

const emit = defineEmits<{
    'go-to-main': [];
}>();

/*
 * Methods.
 */

const goToMain = () => {
    emit('go-to-main');
};
</script>

<template>
    <div class="flex space-x-2">
        <div class="mt-2">
            <CheckCircleIcon class="size-6" />
        </div>
        <div>
            <h2 class="text-lg font-medium">All Routes Successfully Extracted!</h2>
            <p class="mb-2 text-sm">
                No extraction errors found in {{ totalRoutes }} routes.
            </p>
            <AppButton size="xs" @click="goToMain">
                <RouteIcon class="mr-1 size-3" />
                Explore Routes
            </AppButton>
        </div>
    </div>
</template>
