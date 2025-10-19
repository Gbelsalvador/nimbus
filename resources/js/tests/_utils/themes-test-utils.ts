/* eslint-disable @typescript-eslint/no-explicit-any */

import { mount, VueWrapper } from '@vue/test-utils';
import { createPinia, setActivePinia } from 'pinia';
import { describe, expect, it } from 'vitest';
import { Component } from 'vue';
import { createRouter, createWebHistory, Router } from 'vue-router';

/**
 * Test utilities for dark theme testing.
 * Provides helpers to test components in both light and dark modes.
 */

// Mock router for testing
function createMockRouter(): Router {
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
 * Mount a component with dark theme simulation.
 * Adds dark class to the document element to simulate dark mode.
 */
export function mountWithDarkTheme(
    component: Component,
    options: any = {},
): VueWrapper<any> {
    const pinia = createPinia();
    setActivePinia(pinia);

    const router = createMockRouter();

    // Add dark class to document element
    document.documentElement.classList.add('dark');

    const wrapper = mount(component, {
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

    // Store original unmount function
    const originalUnmount = wrapper.unmount.bind(wrapper);

    // Override unmount to clean up dark class
    wrapper.unmount = () => {
        document.documentElement.classList.remove('dark');

        return originalUnmount();
    };

    return wrapper;
}

/**
 * Mount a component with light theme simulation.
 * Ensures dark class is not present on the document element.
 */
export function mountWithLightTheme(
    component: Component,
    options: any = {},
): VueWrapper<any> {
    const pinia = createPinia();
    setActivePinia(pinia);

    const router = createMockRouter();

    // Ensure dark class is not present
    document.documentElement.classList.remove('dark');

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

/**
 * Test a component in both light and dark themes.
 * Runs the same test function for both themes.
 */
export function testBothThemes(
    component: Component,
    testFn: (wrapper: VueWrapper<any>, theme: 'light' | 'dark') => void,
    options: any = {},
) {
    describe('Light Theme', () => {
        it('should render correctly in light theme', () => {
            const wrapper = mountWithLightTheme(component, options);
            testFn(wrapper, 'light');
            wrapper.unmount();
        });
    });

    describe('Dark Theme', () => {
        it('should render correctly in dark theme', () => {
            const wrapper = mountWithDarkTheme(component, options);
            testFn(wrapper, 'dark');
            wrapper.unmount();
        });
    });
}

/**
 * Assert that a component has the correct dark theme classes.
 */
export function expectDarkThemeClasses(
    wrapper: VueWrapper<any>,
    expectedClasses: string[],
) {
    const element = wrapper.element;
    const classList = Array.from(element.classList);

    expectedClasses.forEach(expectedClass => {
        expect(classList).toContain(expectedClass);
    });
}

/**
 * Assert that a component has the correct light theme classes.
 */
export function expectLightThemeClasses(
    wrapper: VueWrapper<any>,
    expectedClasses: string[],
) {
    const element = wrapper.element;
    const classList = Array.from(element.classList);

    expectedClasses.forEach(expectedClass => {
        expect(classList).toContain(expectedClass);
    });
}

/**
 * Common dark theme class patterns for testing.
 */
export const darkThemePatterns = {
    // Background colors
    backgrounds: {
        primary: 'dark:bg-zinc-950',
        secondary: 'dark:bg-zinc-900',
        tertiary: 'dark:bg-zinc-800',
        muted: 'dark:bg-gray-800',
    },

    // Text colors
    text: {
        primary: 'dark:text-zinc-50',
        secondary: 'dark:text-zinc-100',
        muted: 'dark:text-zinc-400',
        mutedSecondary: 'dark:text-gray-300',
    },

    // Border colors
    borders: {
        primary: 'dark:border-zinc-800',
        secondary: 'dark:border-gray-700',
    },

    // Focus states
    focus: {
        ring: 'dark:focus-visible:ring-zinc-300',
        ringOffset: 'dark:ring-offset-zinc-950',
    },

    // Hover states
    hover: {
        primary: 'dark:hover:bg-zinc-800',
        secondary: 'dark:hover:bg-gray-700',
    },
};

/**
 * Common light theme class patterns for testing.
 */
export const lightThemePatterns = {
    // Background colors
    backgrounds: {
        primary: 'bg-white',
        secondary: 'bg-zinc-50',
        tertiary: 'bg-gray-50',
    },

    // Text colors
    text: {
        primary: 'text-zinc-950',
        secondary: 'text-zinc-900',
        muted: 'text-zinc-500',
    },

    // Border colors
    borders: {
        primary: 'border-zinc-200',
        secondary: 'border-gray-100',
    },

    // Focus states
    focus: {
        ring: 'focus-visible:ring-zinc-950',
        ringOffset: 'ring-offset-white',
    },

    // Hover states
    hover: {
        primary: 'hover:bg-zinc-100',
        secondary: 'hover:bg-gray-50',
    },
};
