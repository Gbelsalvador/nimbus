<script setup lang="ts">
import AppGlowingButton from '@/components/base/button/AppGlowingButton.vue';
import { WandSparklesIcon } from 'lucide-vue-next';
</script>

<template>
    <AppGlowingButton>
        <WandSparklesIcon
            class="transition-all will-change-transform group-hover:rotate-12"
        />
        Auto Fill
    </AppGlowingButton>
</template>
