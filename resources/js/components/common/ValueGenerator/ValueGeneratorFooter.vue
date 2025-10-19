<script setup lang="ts">
import { AppBadge } from '@/components/base/badge';
import { useValueGeneratorStore } from '@/stores';
import { ArrowBigUpIcon } from 'lucide-vue-next';

const store = useValueGeneratorStore();
</script>

<template>
    <div
        class="border-t border-zinc-200 bg-zinc-50 p-2 text-center text-xs text-zinc-500 dark:border-zinc-800 dark:bg-zinc-950"
    >
        <!-- Shift-Shift Shortcut Hint -->
        <div
            v-if="!store.wasOpenedViaShiftShift"
            class="mb-2 flex items-center justify-center gap-1.5 text-xs"
        >
            <span>Press</span>

            <AppBadge variant="outline" size="xs" class="flex items-center gap-1">
                <ArrowBigUpIcon class="size-3" />
                Shift
            </AppBadge>

            <span>twice to open this command</span>
        </div>

        <small>Use ↑↓ to navigate, Enter to select, Esc to close</small>
    </div>
</template>
