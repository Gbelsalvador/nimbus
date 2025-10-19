<script setup lang="ts">
import AppPanelStateContainer from '@/components/base/AppPanelStateContainer.vue';
import DocumentationLinkButton from '@/components/domain/DocumentationLinkButton.vue';
</script>

<template>
    <AppPanelStateContainer>
        <h2 class="text-lg font-medium">You didn't perform any requests yet</h2>
        <p class="mb-2 text-sm">
            Make request and the result will appear here. Make sure to check the Wiki in
            case of doubts.
        </p>
        <DocumentationLinkButton />
    </AppPanelStateContainer>
</template>
