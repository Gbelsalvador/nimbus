<script setup lang="ts">
import RoutesListItem from '@/components/domain/RoutesExplorer/RoutesList/RoutesListItem.vue';
import RoutesResource from '@/components/domain/RoutesExplorer/RoutesResourceGroup.vue';
import { RouteDefinition, RoutesGroup } from '@/interfaces/routes/routes';
import { useRequestStore } from '@/stores';
import { computed } from 'vue';

/*
 * Types & interfaces.
 */

interface RoutesListProps {
    routes: RoutesGroup[];
    filteringEnabled: boolean;
}

defineProps<RoutesListProps>();

/*
 * Stores & dependencies.
 */

const requestStore = useRequestStore();

/*
 * Actions.
 */

const setPendingRequest = (route: RouteDefinition, resourceGroup: RoutesGroup) => {
    if (!route || !resourceGroup) {
        return;
    }

    // Get all routes with the same endpoint (different HTTP methods)
    const availableRoutesForEndpoint = resourceGroup.routes.filter(
        (routeInGroup: RouteDefinition) => routeInGroup.endpoint === route.endpoint,
    );

    requestStore.initializeRequest(route, availableRoutesForEndpoint);
};

/*
 * Computed.
 */

const pendingRequestData = computed(() => requestStore.pendingRequestData);

const isRouteActive = (route: RouteDefinition) => {
    if (!route) {
        return false;
    }
    if (!pendingRequestData.value) {
        return false;
    }

    return (
        route.endpoint === pendingRequestData.value.endpoint &&
        route.method === pendingRequestData.value.method
    );
};
</script>

<template>
    <template v-if="routes && routes.length">
        <RoutesResource
            v-for="resourceGroup in routes"
            :key="resourceGroup.resource"
            :resource="resourceGroup.resource"
        >
            <RoutesListItem
                v-for="route in resourceGroup.routes"
                :key="route.endpoint + '-' + route.method"
                :route="route"
                :resource="resourceGroup.resource"
                :on-click="() => setPendingRequest(route, resourceGroup)"
                :is-active="isRouteActive(route)"
            />
        </RoutesResource>
    </template>
    <template v-else-if="!filteringEnabled">
        <div class="px-2">
            <p class="mb-2 text-xs">
                No routes have been detected. Make sure to check the Wiki in case of
                doubts.
            </p>
        </div>
    </template>
    <template v-else>
        <p class="mb-2 px-2 text-xs">No routes matching your keywords.</p>
    </template>
</template>
