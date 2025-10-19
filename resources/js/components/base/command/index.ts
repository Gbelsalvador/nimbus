import { createContext } from 'reka-ui';
import type { Ref } from 'vue';

export { default as AppCommand } from './AppCommand.vue';
export { default as AppCommandDialog } from './AppCommandDialog.vue';
export { default as AppCommandEmpty } from './AppCommandEmpty.vue';
export { default as AppCommandGroup } from './AppCommandGroup.vue';
export { default as AppCommandInput } from './AppCommandInput.vue';
export { default as AppCommandItem } from './AppCommandItem.vue';
export { default as AppCommandList } from './AppCommandList.vue';
export { default as AppCommandSeparator } from './AppCommandSeparator.vue';
export { default as AppCommandShortcut } from './AppCommandShortcut.vue';

export const [useCommand, provideCommandContext] = createContext<{
    allItems: Ref<Map<string, string>>;
    allGroups: Ref<Map<string, Set<string>>>;
    filterState: {
        search: string;
        filtered: {
            count: number;
            items: Map<string, number>;
            groups: Set<string>;
        };
    };
}>('Command');

export const [useCommandGroup, provideCommandGroupContext] = createContext<{
    id?: string;
}>('CommandGroup');
