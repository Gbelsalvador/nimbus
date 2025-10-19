import ValueGenerator from '@/components/common/ValueGenerator/ValueGenerator.vue';
import { mountWithPlugins } from '@/tests/_utils/test-utils';
import { testBothThemes } from '@/tests/_utils/themes-test-utils';
import { Mock } from '@vitest/spy';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import { nextTick, Reactive, reactive } from 'vue';

const mockStore: Reactive<{
    isCommandOpen: boolean;
    currentInputRef: HTMLElement | null;
    generateValue: Mock;
    closeCommand: Mock;
    restoreCommandState: Mock;
    recentGenerators: string[];
}> = reactive({
    isCommandOpen: false,
    currentInputRef: null,
    generateValue: vi.fn(),
    closeCommand: vi.fn(),
    restoreCommandState: vi.fn(),
    recentGenerators: [],
});

const mockComposable = {
    restoreScrollPosition: vi.fn(),
};

vi.mock('@/stores', () => ({
    useValueGeneratorStore: () => mockStore,
}));

vi.mock('@/composables', () => ({
    useTabHorizontalScroll: () => mockComposable,
}));

// Mock DOM methods
const mockGetBoundingClientRect = vi.fn(() => ({
    top: 100,
    bottom: 120,
    left: 50,
    right: 200,
    width: 150,
    height: 20,
}));

Object.defineProperty(window, 'innerHeight', { value: 800 });
Object.defineProperty(HTMLElement.prototype, 'getBoundingClientRect', {
    value: mockGetBoundingClientRect,
});

describe('ValueGenerator', () => {
    beforeEach(() => {
        mockStore.isCommandOpen = false;
        mockStore.currentInputRef = null;
        mockStore.generateValue.mockReset();
        mockStore.closeCommand.mockReset();
        mockComposable.restoreScrollPosition.mockReset();
    });

    it('renders nothing when command is closed', () => {
        const wrapper = mountWithPlugins(ValueGenerator);

        expect(wrapper.find('.fixed.inset-0').exists()).toBe(false);
    });

    it('renders command interface when open', async () => {
        mockStore.isCommandOpen = true;

        const wrapper = mountWithPlugins(ValueGenerator);

        await nextTick();

        expect(wrapper.find('.fixed.inset-0').exists()).toBe(true);
        expect(wrapper.find('[data-slot="command-input"]').exists()).toBe(true);
    });

    it('closes command when clicking outside', async () => {
        mockStore.isCommandOpen = true;

        const wrapper = mountWithPlugins(ValueGenerator);

        await nextTick();

        await wrapper.find('.fixed.inset-0').trigger('click');

        expect(mockStore.closeCommand).toHaveBeenCalled();
    });

    it('does not close command when clicking inside', async () => {
        mockStore.isCommandOpen = true;

        const wrapper = mountWithPlugins(ValueGenerator);
        await nextTick();

        await wrapper.find('.absolute.w-full.max-w-md').trigger('click');

        expect(mockStore.closeCommand).not.toHaveBeenCalled();
    });

    it('closes command on escape key', async () => {
        mockStore.isCommandOpen = true;

        const wrapper = mountWithPlugins(ValueGenerator);

        await nextTick();

        const command = wrapper.findComponent({ name: 'AppCommand' });
        await command.trigger('keydown.escape');

        expect(mockStore.closeCommand).toHaveBeenCalled();
    });

    it('calculates command position correctly when input ref is provided', async () => {
        mockStore.currentInputRef = document.createElement('input');
        mockStore.isCommandOpen = true;

        const wrapper = mountWithPlugins(ValueGenerator);

        await nextTick();

        const commandContent = wrapper.find('.absolute.w-full.max-w-md');
        const style = commandContent.attributes('style')!;

        expect(style).toContain('top: 124px');
        expect(style).toContain('left: 50px');
        expect(style).toContain('transform: none');
    });

    it('positions command above input when no space below', async () => {
        Object.defineProperty(window, 'innerHeight', { value: 200 });

        mockStore.currentInputRef = document.createElement('input');
        mockStore.isCommandOpen = true;

        const wrapper = mountWithPlugins(ValueGenerator);

        await nextTick();

        const commandContent = wrapper.find('.absolute.w-full.max-w-md');
        const style = commandContent.attributes('style')!;

        expect(style).toContain('top: -304px');
    });

    it('uses center position when no input ref', async () => {
        mockStore.isCommandOpen = true;

        const wrapper = mountWithPlugins(ValueGenerator);

        await nextTick();

        const commandContent = wrapper.find('.absolute.w-full.max-w-md');
        const style = commandContent.attributes('style')!;

        expect(style).toContain('top: 50%');
        expect(style).toContain('left: 50%');
        expect(style).toContain('transform: translate(-50%, -50%)');
    });

    it('handles generator selection', async () => {
        const mockValue = 'generated-value';

        mockStore.generateValue.mockReturnValue(mockValue);
        mockStore.isCommandOpen = true;

        const mockInput = document.createElement('input');

        mockInput.value = '';
        mockStore.currentInputRef = mockInput;

        const wrapper = mountWithPlugins(ValueGenerator);

        await nextTick();

        const generatorList = wrapper.findComponent({
            name: 'ValueGeneratorGeneratorList',
        });

        await generatorList.vm.$emit('generator-selected', 'test-generator');

        expect(mockStore.generateValue).toHaveBeenCalledWith('test-generator');
        expect(mockInput.value).toBe(mockValue);
        expect(wrapper.emitted('valueGenerated')?.[0]).toEqual([mockValue]);
        expect(mockStore.closeCommand).toHaveBeenCalled();
    });

    it('does not focus when command is closed', async () => {
        mockStore.isCommandOpen = false;
        mountWithPlugins(ValueGenerator);

        await nextTick();

        expect(mockComposable.restoreScrollPosition).not.toHaveBeenCalled();
    });

    it.skip('focuses command input when command opens', async () => {
        const wrapper = mountWithPlugins(ValueGenerator);

        mockStore.isCommandOpen = true;

        await nextTick();

        // TODO [Test] Make this test works. Problem: the following is not passing.
        await expect
            .poll(() => wrapper.element.querySelector('[data-slot="command-input"]'), {
                timeout: 300,
            })
            .toHaveFocus();
    });

    it('renders all child components', async () => {
        mockStore.isCommandOpen = true;

        const wrapper = mountWithPlugins(ValueGenerator);

        await nextTick();

        expect(wrapper.findComponent({ name: 'AppCommand' }).exists()).toBe(true);

        expect(wrapper.findComponent({ name: 'AppCommandInput' }).exists()).toBe(true);

        expect(
            wrapper.findComponent({ name: 'ValueGeneratorCommandKeepAlive' }).exists(),
        ).toBe(true);

        expect(
            wrapper.findComponent({ name: 'ValueGeneratorCategoryFilters' }).exists(),
        ).toBe(true);

        expect(
            wrapper.findComponent({ name: 'ValueGeneratorGeneratorList' }).exists(),
        ).toBe(true);

        expect(wrapper.findComponent({ name: 'ValueGeneratorFooter' }).exists()).toBe(
            true,
        );
    });

    it('applies correct CSS classes', async () => {
        mockStore.isCommandOpen = true;

        const wrapper = mountWithPlugins(ValueGenerator);

        await nextTick();

        const overlay = wrapper.find('.fixed.inset-0');
        expect(overlay.classes()).toContain('fixed');
        expect(overlay.classes()).toContain('inset-0');
        expect(overlay.classes()).toContain('z-50');

        const commandContent = wrapper.find('.absolute.w-full.max-w-md');
        expect(commandContent.classes()).toContain('absolute');
        expect(commandContent.classes()).toContain('w-full');
        expect(commandContent.classes()).toContain('max-w-md');
    });

    describe('Dark Theme Support', () => {
        testBothThemes(ValueGenerator, wrapper => {
            expect(wrapper.find('.fixed.inset-0').exists()).toBe(false);
        });

        it('renders command interface with proper theming when open', async () => {
            mockStore.isCommandOpen = true;

            const wrapper = mountWithPlugins(ValueGenerator);

            await nextTick();

            const commandContent = wrapper.find('.absolute.w-full.max-w-md');
            expect(commandContent.exists()).toBe(true);

            const command = wrapper.findComponent({ name: 'AppCommand' });
            expect(command.classes()).toContain('rounded-lg');
            expect(command.classes()).toContain('border');
            expect(command.classes()).toContain('shadow-md');
        });

        it('maintains consistent behavior across themes', async () => {
            mockStore.isCommandOpen = true;

            const wrapper = mountWithPlugins(ValueGenerator);

            await nextTick();

            expect(wrapper.find('.fixed.inset-0').exists()).toBe(true);
            expect(wrapper.findComponent({ name: 'AppCommand' }).exists()).toBe(true);
            expect(wrapper.findComponent({ name: 'AppCommandInput' }).exists()).toBe(
                true,
            );
        });
    });
});
