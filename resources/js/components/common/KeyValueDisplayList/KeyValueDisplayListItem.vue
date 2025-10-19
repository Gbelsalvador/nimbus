<script
    setup
    lang="ts"
    generic="T extends { key: string; value: string | number | boolean }"
>
import { AppTableCell, AppTableRow } from '@/components/base/table';
import CopyButton from '@/components/common/CopyButton.vue';
import { useClipboard } from '@vueuse/core';

interface KeyValueDisplayListItemProps {
    item: T;
}

const props = defineProps<KeyValueDisplayListItemProps>();

const { copy, copied } = useClipboard();

const copyValue = () => {
    copy(String(props.item.value));
};
</script>

<template>
    <AppTableRow class="flex-wrap border-0">
        <AppTableCell
            class="pl-panel w-1/4 border-b-1 border-b-transparent align-top font-medium"
        >
            {{ item.key }}
        </AppTableCell>
        <AppTableCell
            class="border-b-1 border-gray-100 align-top break-words dark:border-gray-700"
        >
            <slot name="value" :item="props.item">
                {{ item.value }}
            </slot>
        </AppTableCell>
        <AppTableCell
            class="px-panel w-10 border-b-1 border-gray-100 align-top dark:border-gray-700"
        >
            <CopyButton :on-click="copyValue" :copied="copied" />
        </AppTableCell>
    </AppTableRow>
</template>
