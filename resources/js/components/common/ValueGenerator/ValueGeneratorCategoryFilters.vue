<script setup lang="ts">
import { useTabHorizontalScroll } from '@/composables/ui/useTabHorizontalScroll';
import { useValueGeneratorStore } from '@/stores';

const store = useValueGeneratorStore();

const {
    scrollContainer,
    showLeftMask,
    showRightMask,
    updateScrollMasks,
    scrollTabIntoView,
} = useTabHorizontalScroll();

/**
 * Handles category tab clicks with toggle behavior.
 *
 * Implements toggle behavior where clicking the same category deselects it,
 * while clicking a different category selects it. Works in combination with
 * command component's search filtering.
 */
const handleCategoryClick = (event: Event, categoryId: string) => {
    const isCurrentlySelected = store.commandState.selectedCategory === categoryId;

    const newCategory = isCurrentlySelected
        ? null // <- Deselect the category.
        : categoryId;

    store.setSelectedCategory(newCategory);

    scrollTabIntoView(event.currentTarget as HTMLElement);
};

/**
 * Handles arrow key navigation from category tabs.
 *
 * Intercepts arrow keys on category tabs and redirects focus to the command
 * input for proper keyboard navigation.
 */
const handleCategoryArrowNavigation = (event: KeyboardEvent) => {
    const isArrowKey = event.key === 'ArrowDown' || event.key === 'ArrowUp';

    if (!isArrowKey) {
        return;
    }

    event.preventDefault();
    event.stopPropagation();

    // Move the focus to the command search box for better UX.
    changeFocusToTheCommandSearchBox();
};

const changeFocusToTheCommandSearchBox = () => {
    const commandInput = document.querySelector(
        '[data-slot="command-input"]',
    ) as HTMLInputElement;

    commandInput?.focus();
};

const getCategoryButtonClass = (categoryId: string) => {
    const isSelected = store.commandState.selectedCategory === categoryId;

    return isSelected
        ? 'bg-white text-zinc-950 shadow dark:bg-zinc-950 dark:text-zinc-50'
        : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-50';
};
</script>

<template>
    <!-- Category Filters with horizontal scrolling -->
    <div class="p-2">
        <div class="relative">
            <div
                ref="scrollContainer"
                class="scrollbar-hide bg-subtle-background flex items-center gap-1 overflow-x-auto rounded-lg p-1"
                style="scrollbar-width: none; -ms-overflow-style: none"
                @scroll="updateScrollMasks"
                @keydown="handleCategoryArrowNavigation"
            >
                <button
                    v-for="category in store.categories"
                    :key="category.id"
                    tabindex="0"
                    :class="[
                        'flex flex-shrink-0 items-center justify-center gap-1 rounded-md px-2 py-0.5 text-xs font-medium whitespace-nowrap ring-offset-white transition-all focus-visible:ring-2 focus-visible:ring-zinc-950 focus-visible:ring-offset-2 focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50 dark:ring-offset-zinc-950 dark:focus-visible:ring-zinc-300',
                        getCategoryButtonClass(category.id),
                    ]"
                    @click="event => handleCategoryClick(event, category.id)"
                >
                    <component :is="category.icon" class="size-3" />
                    {{ category.name }}
                </button>
            </div>

            <!-- Scroll Gradient Masks -->
            <div
                v-show="showLeftMask"
                class="pointer-events-none absolute top-0 bottom-0 left-0 w-8 rounded-l-lg bg-gradient-to-r from-zinc-100 via-zinc-100/80 to-transparent transition-opacity duration-200 dark:from-zinc-950 dark:via-zinc-900/80"
            />
            <div
                v-show="showRightMask"
                class="pointer-events-none absolute top-0 right-0 bottom-0 w-8 rounded-r-lg bg-gradient-to-l from-zinc-100 via-zinc-100/80 to-transparent transition-opacity duration-200 dark:from-zinc-950 dark:via-zinc-900/80"
            />
        </div>
    </div>
</template>
