<script setup lang="ts">
import { AppInput } from '@/components/base/input';
import { ModelRef, ref, watch } from 'vue';

type modelType = { username: string; password: string };

const model: ModelRef<modelType> = defineModel<modelType>({
    default: () => ({
        username: '',
        password: '',
    }),
});

const emit = defineEmits(['update:modelValue']);

const username = ref(model.value.username);
const password = ref(model.value.password);

watch(username, newValue => {
    model.value.username = newValue;
    emit('update:modelValue', model.value);
});

watch(password, newValue => {
    model.value.password = newValue;
    emit('update:modelValue', model.value);
});
</script>

<template>
    <div class="grid h-8 grid-cols-3 border-b">
        <label
            class="px-panel flex h-8 items-center border-r py-1 text-xs"
            for="username"
        >
            Username
        </label>
        <AppInput
            id="username"
            v-model="username"
            placeholder="-"
            class="col-span-2 h-full rounded-none border-0 text-xs shadow-none focus:ring-0 focus-visible:ring-0"
        />
    </div>
    <div class="grid h-8 grid-cols-3 border-b">
        <label
            class="px-panel flex h-8 items-center border-r py-1 text-xs"
            for="password"
        >
            Password
        </label>
        <AppInput
            id="password"
            v-model="password"
            placeholder="-"
            class="col-span-2 h-full rounded-none border-0 text-xs shadow-none focus:ring-0 focus-visible:ring-0"
        />
    </div>
</template>
