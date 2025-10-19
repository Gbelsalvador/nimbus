<script setup lang="ts">
import SchemaExtractionErrorButton from '@/components/domain/Client/Errors/SchemaExtractionErrorButton.vue';
import { TriangleAlertIcon } from 'lucide-vue-next';

interface RequestBodyErrorDisplayProps {
    extractionError: string;
}

const props = defineProps<RequestBodyErrorDisplayProps>();
</script>

<template>
    <div
        class="px-panel flex items-center justify-between border-b bg-gradient-to-tr from-red-50 to-transparent to-50% py-1 dark:from-red-900/15"
    >
        <div class="flex items-start gap-2 text-xs">
            <div class="px-1 py-2">
                <TriangleAlertIcon class="size-4 text-red-600 dark:text-red-500" />
            </div>
            <div>
                <span class="flex items-center gap-1 font-bold">Extraction Error</span>
                <p class="text-subtle-foreground mb-0">
                    It wasn't possible to fully extract the shape of this route, there was
                    an error.
                </p>
            </div>
        </div>
        <SchemaExtractionErrorButton :error-html="props.extractionError" />
    </div>
</template>
