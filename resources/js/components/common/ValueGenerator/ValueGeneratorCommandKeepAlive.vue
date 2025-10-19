<script setup lang="ts">
/**
 * State restoration component for command context.
 *
 * This component handles bidirectional synchronization between the ValueGenerator
 * store and the AppCommand's internal filterState. It must be placed inside the
 * AppCommand component to access the command context via useCommand().
 *
 * Why this component is required:
 * - The main ValueGenerator component cannot use useCommand() at the top level
 *   because AppCommand context is only available inside the command component
 * - We need to restore search queries from the store when the command reopens
 * - We need to sync search changes back to the store for future restoration
 */
import { useCommand } from '@/components/base/command';
import { useValueGeneratorStore } from '@/stores';
import { onMounted, watch } from 'vue';

const store = useValueGeneratorStore();
const { filterState } = useCommand();

/**
 * Restores command state when component mounts
 *
 * Uses store method to restore search state, preventing overwriting
 * user input if command already has a query.
 */
onMounted(() => {
    store.restoreCommandState({ filterState });
});

/**
 * Syncs search changes from command back to store.
 *
 * This ensures the store maintains the current search state for when reopening the command.
 */
watch(
    // Vue's watch API requires a getter function, not a direct property reference.
    // Passing filterState.search directly would not work; the closure is necessary.
    () => filterState.search,
    newSearch => {
        store.setSearchQuery(newSearch || '');
    },
);
</script>

<!-- This component only handles state restoration, no UI -->
