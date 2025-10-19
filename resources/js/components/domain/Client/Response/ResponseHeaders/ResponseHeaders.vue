<script setup lang="ts">
import CopyButton from '@/components/common/CopyButton.vue';
import KeyValueDisplayList from '@/components/common/KeyValueDisplayList/KeyValueDisplayList.vue';
import PanelSubHeader from '@/components/layout/PanelSubHeader/PanelSubHeader.vue';
import { HttpHeadersArray } from '@/interfaces/http';
import { useClipboard } from '@vueuse/core';
import { computed } from 'vue';

interface ResponseHeadersProps {
    headers: HttpHeadersArray;
}

const props = defineProps<ResponseHeadersProps>();

const { copy, copied } = useClipboard();

const headersForDisplay = computed(() => {
    return props.headers.map(header => ({
        key: header.key,
        value: header.value ?? '',
    }));
});

const copyAll = () => {
    const copyValue = props.headers.reduce((carry: string, current) => {
        return `${carry}${current.key}: ${current.value ?? ''}\n`;
    }, '');

    copy(copyValue);
};
</script>

<template>
    <PanelSubHeader class="border-b">
        Headers ({{ headers.length }})
        <template #toolbox>
            <div class="px-panel flex-start flex w-10 translate-x-2 items-center">
                <CopyButton :on-click="copyAll" :copied="copied" />
            </div>
        </template>
    </PanelSubHeader>
    <div class="min-h-0 flex-1 overflow-y-auto">
        <KeyValueDisplayList :items="headersForDisplay" />
    </div>
</template>
