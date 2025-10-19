<script setup lang="ts">
import CodeEditor from '@/components/domain/CodeEditor/CodeEditor.vue';
</script>

<template>
    <CodeEditor
        language="plain"
        :readonly="false"
        placeholder="Your Plain Text Content"
    />
</template>
