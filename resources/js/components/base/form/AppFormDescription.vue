<script lang="ts" setup>
import { cn } from '@/utils/ui';
import type { HTMLAttributes } from 'vue';
import { useFormField } from './useFormField';

const props = defineProps<{
    class?: HTMLAttributes['class'];
}>();

const { formDescriptionId } = useFormField();
</script>

<template>
    <p :id="formDescriptionId" :class="cn('text-subtle text-sm', props.class)">
        <slot />
    </p>
</template>
