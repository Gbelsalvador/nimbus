<script setup lang="ts">
import { AppSidebarMenuButton } from '@/components/base/sidebar';
import HttpVerbLabel from '@/components/domain/HttpVerbLabel/HttpVerbLabel.vue';
import { RouteDefinition } from '@/interfaces/routes/routes';
import { computed } from 'vue';

interface RoutesListItemProps {
    route: RouteDefinition;
    resource: string;
    isActive: boolean;
    onClick?: () => void;
}

const props = withDefaults(defineProps<RoutesListItemProps>(), {
    isActive: false,
    onClick: () => {},
});

const emit = defineEmits<{
    click: [];
}>();

const handleClick = () => {
    if (props.onClick) {
        props.onClick();
    }

    emit('click');
};

const endpointsSegments = computed(() => {
    const segments = props.route.shortEndpoint
        .replace(`${props.resource}`, '')
        .split('/');

    if (segments.length > 1 && segments[0] === '') {
        segments.shift();
    }

    return segments.map(segment => {
        if (!segment.startsWith('{')) {
            return {
                value: `/${segment}`,
                isRouteVariable: false,
            };
        }

        return {
            value: `/${segment}`,
            isRouteVariable: true,
        };
    });
});
</script>

<template>
    <AppSidebarMenuButton
        :is-active="isActive"
        class="text-sm data-[active=true]:rounded-l-none"
        @click="handleClick"
    >
        <HttpVerbLabel :method="route.method" />
        <span class="whitespace-nowrap">
            <template v-for="(segment, index) in endpointsSegments" :key="index">
                <span v-if="!segment.isRouteVariable">{{ segment.value }}</span>
                <span v-else>
                    <span class="text-zinc-400">{{ segment.value }}</span>
                </span>
            </template>
        </span>
    </AppSidebarMenuButton>
</template>
