/* eslint-disable @typescript-eslint/no-explicit-any */

import { mount, VueWrapper } from '@vue/test-utils';
import { createPinia, setActivePinia } from 'pinia';
import { Component } from 'vue';
import { createRouter, createWebHistory, Router } from 'vue-router';

/*
 * Custom test utilities for consistent Vue component testing.
 * Provides common setup patterns and helper functions.
 */

export function createMockRouter(): Router {
    return createRouter({
        history: createWebHistory(),
        routes: [
            {
                path: '/',
                name: 'home',
                component: { template: '<div>Home</div>' },
            },
            {
                path: '/main',
                name: 'main',
                component: { template: '<div>Main</div>' },
            },
            {
                path: '/status',
                name: 'status',
                component: { template: '<div>Status</div>' },
            },
        ],
    });
}

/**
 * Mount a Vue component with common test setup.
 * Includes Pinia store, router, and global stubs.
 */
export function mountWithPlugins(
    component: Component,
    options: any = {},
): VueWrapper<any> {
    const pinia = createPinia();
    setActivePinia(pinia);

    const router = createMockRouter();

    return mount(component, {
        global: {
            plugins: [pinia, router],
            stubs: {
                'router-link': true,
                'router-view': true,
                'keep-alive': true,
                transition: true,
                'transition-group': true,
                teleport: true,
            },
        },
        ...options,
    });
}
