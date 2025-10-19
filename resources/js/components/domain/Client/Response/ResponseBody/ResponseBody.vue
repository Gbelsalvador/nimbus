<script setup lang="ts">
import CodeEditor from '@/components/domain/CodeEditor/CodeEditor.vue';

interface ResponseBodyProps {
    content: string;
}

defineProps<ResponseBodyProps>();
</script>

<template>
    <CodeEditor
        language="json"
        :readonly="true"
        :placeholder="content === '' ? 'Empty' : 'Response JSON Payload'"
        :model-value="content"
        :disabled="content === ''"
    />
</template>
