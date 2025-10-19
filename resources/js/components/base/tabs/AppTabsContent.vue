<script setup lang="ts">
import { cn } from '@/utils/ui';
import { TabsContent, type TabsContentProps } from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';

const props = defineProps<TabsContentProps & { class?: HTMLAttributes['class'] }>();

const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props;

    return delegated;
});
</script>

<template>
    <TabsContent
        :class="cn('mt-2 ring-offset-white focus-visible:outline-none', props.class)"
        v-bind="delegatedProps"
    >
        <slot />
    </TabsContent>
</template>
