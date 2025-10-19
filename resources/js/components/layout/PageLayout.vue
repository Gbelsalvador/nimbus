<script setup lang="ts">
defineOptions({
    name: 'PageLayout',
});

import type { Component } from 'vue';

interface Props {
    title: string;
    icon?: Component;
    scrollable?: boolean;
}

withDefaults(defineProps<Props>(), {
    scrollable: true,
    icon: undefined,
});
</script>

<template>
    <div class="flex h-screen max-h-screen flex-col">
        <!-- Header -->
        <div class="h-toolbar flex items-center overflow-hidden border-b p-0">
            <div class="px-panel flex items-center">
                <component :is="icon" v-if="icon" class="mr-2 size-4" />
                <span class="text-sm font-medium">{{ title }}</span>
            </div>

            <div class="px-panel ml-auto flex items-center">
                <slot name="header-actions" />
            </div>
        </div>

        <!-- Sub Header -->
        <div
            class="px-panel h-sub-toolbar bg-subtle-background flex items-center justify-between border-b"
        >
            <slot name="subheader-left" />
            <slot name="subheader-right" />
        </div>

        <!-- Content Area -->
        <div class="flex-1 overflow-hidden">
            <slot name="content" />
        </div>
    </div>
</template>
