<script setup lang="ts">
import {
    AppSelect,
    AppSelectContent,
    AppSelectItem,
    AppSelectTrigger,
    AppSelectValue,
} from '@/components/base/select';

defineProps<{
    versions: string[];
}>();

const model = defineModel<string>();
</script>

<template>
    <AppSelect v-model="model" class="flex items-center text-sm">
        <AppSelectTrigger
            class="h-sub-toolbar w-full rounded-none border-0 border-b shadow-none focus:ring-0 focus-visible:outline-none"
        >
            <AppSelectValue placeholder="Select API Version" />
        </AppSelectTrigger>
        <AppSelectContent>
            <AppSelectItem v-for="value in versions" :key="value" :value="value">
                {{ value }}
            </AppSelectItem>
        </AppSelectContent>
    </AppSelect>
</template>
