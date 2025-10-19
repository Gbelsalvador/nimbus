<script setup lang="ts">
import CopyButton from '@/components/common/CopyButton.vue';
import { ExceptionPrevious } from '@/interfaces/routes';
import { useClipboard } from '@vueuse/core';

interface TechnicalDetailsSectionProps {
    previousError: ExceptionPrevious;
}

const props = defineProps<TechnicalDetailsSectionProps>();

const { copy, copied } = useClipboard();

const copyValue = () => {
    let value = props.previousError.message;

    if (props.previousError.file) {
        value += `\n${props.previousError.file}::${props.previousError.line}`;
    }

    if (props.previousError.trace) {
        value += `\n${props.previousError.trace}`;
    }

    copy(String(value));
};
</script>

<template>
    <div>
        <h3 class="mb-2 font-semibold text-gray-900 dark:text-gray-100">
            Technical Details
            <CopyButton :on-click="copyValue" :copied="copied" />
        </h3>
        <div class="rounded-lg bg-red-50 p-4 dark:bg-red-950/30">
            <p class="font-mono text-sm text-red-800 dark:text-red-200">
                {{ previousError.message }}
            </p>
            <div
                v-if="previousError.file"
                class="mt-2 text-sm wrap-break-word text-red-600 dark:text-red-400"
            >
                <p>{{ previousError.file }}:{{ previousError.line }}</p>
                <div v-if="previousError.trace" class="mt-2">
                    Trace:
                    <br />
                    <!-- eslint-disable vue/no-v-html -->
                    <span
                        class="text-subtle-foreground block"
                        v-html="previousError.trace"
                    ></span>
                    <!-- eslint-enable vue/no-v-html -->
                </div>
            </div>
        </div>
    </div>
</template>
