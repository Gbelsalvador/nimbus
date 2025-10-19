import KeyValueParameters from '@/components/common/KeyValueParameters/KeyValueParameters.vue';
import { mountWithPlugins } from '@/tests/_utils/test-utils';
import { describe, expect, it, vi } from 'vitest';
import { nextTick } from 'vue';

// Mock the composables and stores
const mockKeyValueComposable = {
    parameters: [
        {
            id: '1',
            key: 'test-key',
            value: 'test-value',
            enabled: true,
            type: 'text',
            deleting: false,
        },
        {
            id: '2',
            key: 'another-key',
            value: 'another-value',
            enabled: false,
            type: 'text',
            deleting: false,
        },
    ],
    deletingAll: false,
    areAllParametersDisabled: false,
    addNewEmptyParameter: vi.fn(),
    toggleAllParametersEnabledState: vi.fn(),
    triggerParameterDeletion: vi.fn(),
    deleteAllParameters: vi.fn(),
    isParameterMarkedForDeletion: vi.fn(),
};

const mockValueGeneratorStore = {
    openCommand: vi.fn(),
    closeCommand: vi.fn(),
};

vi.mock('@/composables/ui/useKeyValueParameters', () => ({
    useKeyValueParameters: () => mockKeyValueComposable,
}));

vi.mock('@/stores', () => ({
    useValueGeneratorStore: () => mockValueGeneratorStore,
}));

const componentFactory = (props = {}) =>
    mountWithPlugins(KeyValueParameters, {
        props: { modelValue: [], ...props },
    });

describe('KeyValueParameters - Rendering', () => {
    it('renders component container', () => {
        const wrapper = componentFactory();
        expect(wrapper.find('[data-testid="kv-container"]').exists()).toBe(true);
    });

    it('renders type selector when freeFormTypes is enabled', () => {
        const wrapper = componentFactory({ freeFormTypes: true });
        expect(wrapper.find('[data-testid="type-selector"]').exists()).toBe(true);
    });

    it('renders parameters list correctly', () => {
        const wrapper = componentFactory();
        const rows = wrapper.findAll('[data-testid="parameter-row"]');
        expect(rows).toHaveLength(2); // based on mock data
    });

    it('renders header action buttons', () => {
        const wrapper = componentFactory();
        expect(wrapper.find('[data-testid="add-button"]').exists()).toBe(true);
        expect(wrapper.find('[data-testid="enable-all-button"]').exists()).toBe(true);
        expect(wrapper.find('[data-testid="delete-all-button"]').exists()).toBe(true);
    });

    it('applies red styling to Delete All button when deletingAll is true', () => {
        mockKeyValueComposable.deletingAll = true;

        const deleteButton = componentFactory().find('[data-testid="delete-all-button"]');
        expect(deleteButton.classes()).toContain('!text-red-500');
        expect(deleteButton.classes()).toContain('dark:!text-rose-700');
        expect(deleteButton.classes()).toContain('hover:text-red-500');
        expect(deleteButton.classes()).toContain('dark:hover:text-red-700');
    });
});

describe('KeyValueParameters - Actions', () => {
    it('calls addNewEmptyParameter when Add button is clicked', async () => {
        const wrapper = componentFactory();
        await wrapper.find('[data-testid="add-button"]').trigger('click');
        expect(wrapper.vm.addNewEmptyParameter).toHaveBeenCalled();
    });

    it('calls toggleAllParametersEnabledState when Enable/Disable All clicked', async () => {
        const wrapper = componentFactory();
        await wrapper.find('[data-testid="enable-all-button"]').trigger('click');
        expect(wrapper.vm.toggleAllParametersEnabledState).toHaveBeenCalled();
    });

    it('calls deleteAllParameters when Delete All clicked', async () => {
        const wrapper = componentFactory();
        await wrapper.find('[data-testid="delete-all-button"]').trigger('click');
        expect(wrapper.vm.deleteAllParameters).toHaveBeenCalled();
    });

    it('shows correct text for Enable/Disable All button', () => {
        mockKeyValueComposable.areAllParametersDisabled = true;
        expect(componentFactory().text()).toContain('Enable All');

        mockKeyValueComposable.areAllParametersDisabled = false;
        expect(componentFactory().text()).toContain('Disable All');
    });
});

describe('KeyValueParameters - Focus/Blur & Generator', () => {
    it('handles input focus correctly', async () => {
        const wrapper = componentFactory();
        const input = wrapper.find('[data-testid="kv-value"]');
        await input.trigger('focus');
        expect(wrapper.vm.focusedInputIndex).toBe(0);
        expect(wrapper.vm.focusedInputRef).toBe(input.element);
    });

    it('handles input blur correctly', async () => {
        const wrapper = componentFactory();
        wrapper.vm.focusedInputIndex = 1;
        wrapper.vm.focusedInputRef = document.createElement('input');
        const input = wrapper.find('[data-testid="kv-value"]');
        await input.trigger('blur');
        expect(wrapper.vm.focusedInputIndex).toBeNull();
        expect(wrapper.vm.focusedInputRef).toBeNull();
    });

    it('prevents blur when focus moves to value generator menu', async () => {
        const wrapper = componentFactory();

        const dummyInput = document.createElement('input');

        wrapper.vm.focusedInputIndex = 0;
        wrapper.vm.focusedInputRef = dummyInput;

        const mockTarget = document.createElement('div');
        mockTarget.setAttribute('data-ValueGenerator-focus-hook', '');
        mockTarget.closest = vi.fn().mockReturnValue(mockTarget);

        const input = wrapper.find('[data-testid="kv-value"]');
        await input.trigger('blur', { relatedTarget: mockTarget });

        expect(wrapper.vm.focusedInputIndex).toBe(0);
        expect(wrapper.vm.focusedInputRef).toBe(dummyInput);
    });

    it('handles generator click correctly', async () => {
        const wrapper = componentFactory();
        const input = wrapper.find('[data-testid="kv-value"]');

        wrapper.vm.focusedInputIndex = 0;
        wrapper.vm.focusedInputRef = input;

        await nextTick();

        await wrapper
            .find('[data-testid="generator-button"]')
            .trigger('mousedown', { preventDefault: vi.fn() });
        expect(wrapper.vm.openCommand).toHaveBeenCalledWith(input);
    });
});

describe('KeyValueParameters - v-model', () => {
    it('handles v-model updates correctly', async () => {
        const initialValue = [
            { key: 'test', value: 'value', enabled: true, type: 'text' },
        ];
        const wrapper = componentFactory({ modelValue: initialValue });
        expect(wrapper.vm.modelRef).toEqual(initialValue);

        const newValue = [
            ...initialValue,
            { key: 'new', value: 'new-value', enabled: true, type: 'text' },
        ];
        await wrapper.setProps({ modelValue: newValue });
        expect(wrapper.vm.modelRef).toEqual(newValue);
    });
});
