<script setup lang="ts">
import AppPanelRipple from '@/components/base/AppPanelRipple.vue';
import { AppBadge } from '@/components/base/badge';
import { AppButton } from '@/components/base/button';
import { RouteExtractorException } from '@/interfaces/routes';
import { Loader2Icon, RefreshCcwIcon, SkipForwardIcon } from 'lucide-vue-next';
import { ref } from 'vue';
import ErrorCardHeader from './RouteExtractor/ErrorCardHeader.vue';
import RouteInformationSection from './RouteExtractor/RouteInformationSection.vue';
import SuggestedSolutionCallout from './RouteExtractor/SuggestedSolutionCallout.vue';
import TechnicalDetailsSection from './RouteExtractor/TechnicalDetailsSection.vue';

interface RoutesErrorProps {
    error: RouteExtractorException;
}

const props = defineProps<RoutesErrorProps>();

const isIgnoring = ref(false);

const handleRetry = () => {
    window.location.reload();
};

const handleIgnoreEndpoint = () => {
    if (isIgnoring.value) {
        return;
    }

    if (!props.error.ignoreData) {
        return;
    }

    isIgnoring.value = true;
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('ignore', props.error.ignoreData);
    window.location.href = currentUrl.toString();
};
</script>

<template>
    <div
        class="relative h-full max-h-full w-full bg-gradient-to-br from-red-50/30 to-transparent p-4 dark:from-red-950/10"
    >
        <div class="relative z-10 flex h-full w-full justify-center overflow-auto py-2">
            <div class="flex h-full flex-col space-y-4">
                <div class="space-y-2">
                    <AppBadge variant="outline" class="p-0 px-1 text-xs">
                        Internal Error
                    </AppBadge>
                    <h1 class="text-xl font-medium text-red-500 dark:text-rose-600">
                        An error occurred while processing your application routes
                    </h1>
                </div>

                <SuggestedSolutionCallout
                    v-if="error.suggestedSolution"
                    :solution="error.suggestedSolution"
                />

                <div
                    class="relative flex flex-1 flex-col overflow-hidden rounded-xl border-1 bg-white/10 break-words backdrop-blur-md dark:bg-gray-950/10"
                >
                    <ErrorCardHeader :message="error.exception.message" />
                    <div
                        class="relative z-10 flex flex-1 flex-col space-y-4 overflow-auto p-4"
                    >
                        <RouteInformationSection :route-context="error.routeContext" />
                        <TechnicalDetailsSection
                            v-if="error.exception.previous"
                            class="flex-1"
                            :previous-error="error.exception.previous"
                        />
                    </div>

                    <div class="flex gap-4 px-4 py-2">
                        <AppButton
                            variant="outline"
                            :disabled="isIgnoring"
                            @click="handleRetry"
                        >
                            <RefreshCcwIcon />
                            Retry
                        </AppButton>
                        <AppButton
                            variant="default"
                            :disabled="isIgnoring"
                            @click="handleIgnoreEndpoint"
                        >
                            <Loader2Icon v-if="isIgnoring" class="animate-spin" />
                            <SkipForwardIcon v-else />
                            {{ isIgnoring ? 'Ignoring...' : 'Ignore this Endpoint' }}
                        </AppButton>
                    </div>
                </div>
            </div>
        </div>
        <AppPanelRipple />
    </div>
</template>
