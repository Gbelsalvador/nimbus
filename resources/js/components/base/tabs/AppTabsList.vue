<script setup lang="ts">
import { cn } from '@/utils/ui';
import { TabsList, type TabsListProps } from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';

const props = defineProps<TabsListProps & { class?: HTMLAttributes['class'] }>();

const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props;

    return delegated;
});
</script>

<template>
    <TabsList
        v-bind="delegatedProps"
        :class="
            cn(
                'text-subtle inline-flex items-center justify-center rounded-sm p-1',
                props.class,
            )
        "
    >
        <slot />
    </TabsList>
</template>
