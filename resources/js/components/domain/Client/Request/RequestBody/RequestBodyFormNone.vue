<script setup lang="ts">
import {
    AppCard,
    AppCardDescription,
    AppCardHeader,
    AppCardTitle,
} from '@/components/base/card';
import { CircleSlash2Icon } from 'lucide-vue-next';
import { onMounted } from 'vue';

const emit = defineEmits(['update:modelValue']);

onMounted(() => emit('update:modelValue', null));
</script>

<template>
    <AppCard
        class="px-panel bg-background relative flex items-start gap-2 rounded-none border-0 border-b py-2.5 shadow-none"
    >
        <CircleSlash2Icon />
        <AppCardHeader class="p-0">
            <AppCardTitle>No Body</AppCardTitle>
            <AppCardDescription>
                You're performing the request without a Payload.
            </AppCardDescription>
        </AppCardHeader>
    </AppCard>
</template>
