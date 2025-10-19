import type { Component } from 'vue';

export enum ValueGeneratorCommandOpenMethod {
    CLICK = 'click',
    SHIFT_SHIFT = 'shift-shift',
}

export interface ValueGenerator {
    id: string;
    name: string;
    description: string;
    category: GeneratorCategory;
    generate: (config?: object) => string | number | bigint;
    shortcut?: string;
    icon?: Component; // <- Lucide icon component
}

export interface GeneratorCategory {
    id: string;
    name: string;
    icon?: Component; // <- Lucide icon component
}

export interface GeneratorCommandState {
    searchQuery: string;
    selectedCategory: string | null;
    recentGenerators: string[];
}
