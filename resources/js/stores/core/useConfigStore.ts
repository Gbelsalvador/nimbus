import { defineStore } from 'pinia';
import { computed, ref } from 'vue';

export interface CurrentUser {
    id: string | number;
}

export interface AppConfig {
    urlBase: string;
    basePath: string;
    globalHeaders: Array<{
        header: string;
        type: 'raw' | 'generator';
        value: string | number;
    }>;
    isVersioned: boolean;
    currentUser: CurrentUser | null;
}

export const useConfigStore = defineStore('config', () => {
    // Default configuration
    const config = ref<AppConfig>({
        urlBase: (window.Nimbus?.apiBaseUrl as string) || 'http://localhost',
        isVersioned: (window.Nimbus?.isVersioned as boolean) || false,
        basePath: (window.Nimbus?.basePath as string) || '',
        globalHeaders: window.Nimbus?.headers
            ? JSON.parse(window.Nimbus.headers as string)
            : [],
        currentUser: window.Nimbus?.currentUser
            ? JSON.parse(window.Nimbus.currentUser)
            : null,
    });

    // Computed values
    const apiUrl = computed(() => config.value.urlBase);
    const appBasePath = computed(() => config.value.basePath);
    const headers = computed(() => config.value.globalHeaders);
    const isVersioned = computed(() => config.value.isVersioned);
    const isLoggedIn = computed(() => config.value.currentUser !== null);
    const userId = computed(() => config.value.currentUser?.id ?? null);

    return {
        // State
        config,

        // Getters
        apiUrl,
        appBasePath,
        headers,
        isVersioned,
        isLoggedIn,
        userId,
    };
});
