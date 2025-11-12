<script setup lang="ts">
import { AppButton } from '@/components/base/button';
import { ExternalLinkIcon } from 'lucide-vue-next';
</script>

<template>
    <AppButton
        size="xs"
        as="a"
        href="https://github.com/sunchayn/nimbus/tree/base/wiki/user-guide"
        target="_blank"
    >
        <ExternalLinkIcon />
        Documentation
    </AppButton>
</template>
