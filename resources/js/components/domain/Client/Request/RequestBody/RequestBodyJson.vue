<script setup lang="ts">
import CodeEditor from '@/components/domain/CodeEditor/CodeEditor.vue';
import { JsonSchema } from '@/interfaces/schema/shape';

defineProps<RequestBodyJsonProps>();

const model = defineModel<string>({
    default: () => '',
});

interface RequestBodyJsonProps {
    schema: JsonSchema | undefined;
}
</script>

<template>
    <CodeEditor
        v-model="model"
        language="json"
        :readonly="false"
        placeholder="Your JSON Payload"
        :validation-schema="schema"
    />
</template>
