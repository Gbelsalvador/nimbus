<script setup lang="ts">
import type { CollapsibleRootEmits, CollapsibleRootProps } from 'reka-ui';
import { CollapsibleRoot, useForwardPropsEmits } from 'reka-ui';

const props = defineProps<CollapsibleRootProps>();
const emits = defineEmits<CollapsibleRootEmits>();

const forwarded = useForwardPropsEmits(props, emits);
</script>

<template>
    <!-- eslint-disable-next-line vue/no-template-shadow -->
    <CollapsibleRoot v-slot="{ open }" v-bind="forwarded">
        <slot :open="open" />
    </CollapsibleRoot>
</template>
