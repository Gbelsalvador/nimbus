<script setup lang="ts">
import AppRipple from '@/components/base/ripple/AppRipple.vue';
</script>

<template>
    <div class="relative">
        <slot />
        <AppRipple />
    </div>
</template>
