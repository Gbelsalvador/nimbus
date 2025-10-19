import { AppInput } from '@/components/base/input';
import { mountWithPlugins } from '@/tests/_utils/test-utils';
import { describe, expect, it } from 'vitest';

describe('AppInput Snapshots', () => {
    it('renders default input snapshot', () => {
        const wrapper = mountWithPlugins(AppInput);

        expect(wrapper.html()).toMatchSnapshot();
    });

    it('renders input with value snapshot', () => {
        const wrapper = mountWithPlugins(AppInput, {
            props: { modelValue: 'test value' },
        });

        expect(wrapper.html()).toMatchSnapshot();
    });

    it('renders email input snapshot', () => {
        const wrapper = mountWithPlugins(AppInput, {
            props: { type: 'email' },
        });

        expect(wrapper.html()).toMatchSnapshot();
    });

    it('renders disabled input snapshot', () => {
        const wrapper = mountWithPlugins(AppInput, {
            props: { disabled: true },
        });

        expect(wrapper.html()).toMatchSnapshot();
    });

    it('renders input with placeholder snapshot', () => {
        const wrapper = mountWithPlugins(AppInput, {
            props: { placeholder: 'Enter your text here' },
        });

        expect(wrapper.html()).toMatchSnapshot();
    });

    it('renders input with custom class snapshot', () => {
        const wrapper = mountWithPlugins(AppInput, {
            props: { class: 'custom-input-class' },
        });

        expect(wrapper.html()).toMatchSnapshot();
    });

    it('renders file input snapshot', () => {
        const wrapper = mountWithPlugins(AppInput, {
            props: { type: 'file' },
        });

        expect(wrapper.html()).toMatchSnapshot();
    });

    it('renders number input snapshot', () => {
        const wrapper = mountWithPlugins(AppInput, {
            props: { type: 'number', modelValue: 42 },
        });

        expect(wrapper.html()).toMatchSnapshot();
    });

    it('renders input with defaultValue snapshot', () => {
        const wrapper = mountWithPlugins(AppInput, {
            props: { defaultValue: 'default text' },
        });

        expect(wrapper.html()).toMatchSnapshot();
    });

    it('renders all input types snapshot', () => {
        const types = [
            'text',
            'email',
            'password',
            'number',
            'tel',
            'url',
            'search',
            'file',
        ] as const;

        types.forEach(type => {
            const wrapper = mountWithPlugins(AppInput, {
                props: { type },
            });

            expect(wrapper.html()).toMatchSnapshot(`input-type-${type}`);
        });
    });
});
