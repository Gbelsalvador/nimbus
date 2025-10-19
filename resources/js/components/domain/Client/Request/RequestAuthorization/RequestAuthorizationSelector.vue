<script setup lang="ts">
import {
    AppSelect,
    AppSelectContent,
    AppSelectGroup,
    AppSelectItem,
    AppSelectLabel,
    AppSelectTrigger,
    AppSelectValue,
} from '@/components/base/select';
import { AuthorizationType, AuthorizationTypeItem } from '@/interfaces/generated';
import { useRequestStore, useSettingsStore } from '@/stores';
import { watchOnce } from '@vueuse/core';
import { SparklesIcon } from 'lucide-vue-next';
import { computed, ModelRef } from 'vue';

interface Props {
    types: {
        special: readonly AuthorizationTypeItem[];
        traditional: readonly AuthorizationTypeItem[];
    };
}

const props = defineProps<Props>();

const model: ModelRef<AuthorizationType> = defineModel<AuthorizationType>({
    default: () => AuthorizationType.CurrentUser,
});

/*
 * Stores.
 */

const requestStore = useRequestStore();
const settingsStore = useSettingsStore();

const pendingRequestData = computed(() => requestStore.pendingRequestData);

watchOnce(
    pendingRequestData,
    newValue => {
        model.value =
            newValue?.authorization.type ??
            settingsStore.preferences.defaultAuthorizationType;
    },
    { deep: true },
);
</script>

<template>
    <div class="flex items-center">
        <span class="text-muted-foreground w-[120px] text-xs">Authorization Type:</span>
        <AppSelect v-model="model">
            <AppSelectTrigger
                class="min-w-[120px] rounded-none border-0 text-xs shadow-none focus:ring-0"
            >
                <AppSelectValue />
            </AppSelectTrigger>
            <AppSelectContent>
                <AppSelectGroup>
                    <AppSelectLabel>
                        <div class="flex items-center">
                            <SparklesIcon :size="14" class="mr-2" />
                            Special
                        </div>
                    </AppSelectLabel>
                    <AppSelectItem
                        v-for="type in props.types.special"
                        :key="type.id"
                        :value="type.id"
                    >
                        {{ type.label }}
                    </AppSelectItem>
                </AppSelectGroup>
                <AppSelectGroup>
                    <AppSelectLabel>Other</AppSelectLabel>
                    <AppSelectItem
                        v-for="type in props.types.traditional"
                        :key="type.id"
                        :value="type.id"
                    >
                        {{ type.label }}
                    </AppSelectItem>
                </AppSelectGroup>
            </AppSelectContent>
        </AppSelect>
    </div>
</template>
