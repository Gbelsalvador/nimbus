<script type="ts" setup>
import { AppSidebarHeader } from "@/components/base/sidebar";
import RouteExplorerBranding from "@/components/domain/RoutesExplorer/RouteExplorerBranding.vue";
</script>

<template>
    <AppSidebarHeader
        class="h-toolbar flex flex-row items-center justify-between border-b"
    >
        <RouteExplorerBranding />
    </AppSidebarHeader>
</template>
