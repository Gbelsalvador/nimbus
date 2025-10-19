<script setup lang="ts">
import { AppButton } from '@/components/base/button';
import { CheckIcon, CopyIcon } from 'lucide-vue-next';

interface CopyButtonProps {
    onClick: () => void;
    copied: boolean;
}

defineProps<CopyButtonProps>();
</script>

<template>
    <AppButton size="xs" variant="ghost" class="size-6" @click="onClick">
        <CopyIcon v-if="!copied" class="size-3" />
        <CheckIcon v-else class="size-3 text-green-800 dark:text-green-500" />
    </AppButton>
</template>
