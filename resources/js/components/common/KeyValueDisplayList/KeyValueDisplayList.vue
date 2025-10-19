<script
    setup
    lang="ts"
    generic="T extends { key: string; value: string | number | boolean }"
>
import { AppTable, AppTableBody } from '@/components/base/table';
import KeyValueDisplayListItem from '@/components/common/KeyValueDisplayList/KeyValueDisplayListItem.vue';

interface KeyValueDisplayListProps {
    items: Array<T>;
}

defineProps<KeyValueDisplayListProps>();
</script>

<template>
    <AppTable class="table-fixed text-xs">
        <AppTableBody class="max-w-full">
            <KeyValueDisplayListItem
                v-for="(displayItem, index) in items"
                :key="index"
                :item="displayItem"
            >
                <template #value="{ item }">
                    <slot name="value" :item="item" />
                </template>
            </KeyValueDisplayListItem>
        </AppTableBody>
    </AppTable>
</template>
