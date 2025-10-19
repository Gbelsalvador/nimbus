<script lang="ts" setup>
import { AppLabel } from '@/components/base/label';
import { cn } from '@/utils/ui';
import type { LabelProps } from 'reka-ui';
import type { HTMLAttributes } from 'vue';
import { useFormField } from './useFormField';

const props = defineProps<LabelProps & { class?: HTMLAttributes['class'] }>();

const { error, formItemId } = useFormField();
</script>

<template>
    <AppLabel
        :class="cn(error && 'text-red-500 dark:text-red-900', props.class)"
        :for="formItemId"
    >
        <slot />
    </AppLabel>
</template>
