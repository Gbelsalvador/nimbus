<script setup lang="ts">
import {
    AppTabs,
    AppTabsContent,
    AppTabsList,
    AppTabsTrigger,
} from '@/components/base/tabs';
import {
    RequestAuthorization,
    RequestBody,
    RequestBuilderEndpoint,
    RequestHeaders,
    RequestParameters,
} from '@/components/domain/Client/Request';
</script>

<template>
    <div
        class="relative flex h-full max-h-full flex-1 flex-col"
        data-testid="request-builder-root"
    >
        <RequestBuilderEndpoint class="h-toolbar border-b" />
        <AppTabs
            default-value="body"
            class="mt-0 flex flex-1 flex-col overflow-hidden"
            data-testid="app-tabs-container"
        >
            <div class="bg-subtle-background border-b">
                <AppTabsList class="h-toolbar px-panel rounded-none">
                    <AppTabsTrigger value="parameters" label="Parameters" />
                    <AppTabsTrigger value="body" label="Body" />
                    <AppTabsTrigger value="authorization" label="Authorization" />
                    <AppTabsTrigger value="headers" label="Headers" />
                </AppTabsList>
            </div>
            <AppTabsContent
                value="parameters"
                class="mt-0 flex max-h-full min-h-0 flex-1 flex-col"
            >
                <RequestParameters />
            </AppTabsContent>
            <AppTabsContent
                value="body"
                class="mt-0 flex max-h-full min-h-0 flex-1 flex-col"
            >
                <RequestBody />
            </AppTabsContent>
            <AppTabsContent
                value="authorization"
                class="mt-0 flex max-h-full min-h-0 flex-1 flex-col"
            >
                <RequestAuthorization />
            </AppTabsContent>
            <AppTabsContent
                value="headers"
                class="mt-0 flex max-h-full min-h-0 flex-1 flex-col"
            >
                <RequestHeaders />
            </AppTabsContent>
        </AppTabs>
    </div>
</template>
