<script setup lang="ts">
import { AppButton } from '@/components/base/button';
import { cn } from '@/utils/ui';
import { PanelLeft } from 'lucide-vue-next';
import type { HTMLAttributes } from 'vue';
import { useSidebar } from './utils';

const props = defineProps<{
    class?: HTMLAttributes['class'];
}>();

const { toggleSidebar } = useSidebar();
</script>

<template>
    <AppButton
        data-sidebar="trigger"
        variant="ghost"
        size="icon"
        :class="cn('size-8', props.class)"
        @click="toggleSidebar"
    >
        <PanelLeft />
        <span class="sr-only">Toggle Sidebar</span>
    </AppButton>
</template>
