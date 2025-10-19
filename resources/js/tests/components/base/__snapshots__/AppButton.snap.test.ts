import { AppButton } from '@/components/base/button';
import { mountWithPlugins } from '@/tests/_utils/test-utils';
import { describe, expect, it } from 'vitest';

describe('AppButton Snapshots', () => {
    it('renders default button snapshot', () => {
        const wrapper = mountWithPlugins(AppButton, {
            slots: { default: 'Default Button' },
        });

        expect(wrapper.html()).toMatchSnapshot();
    });

    it('renders destructive variant snapshot', () => {
        const wrapper = mountWithPlugins(AppButton, {
            props: { variant: 'destructive' },
            slots: { default: 'Delete Button' },
        });

        expect(wrapper.html()).toMatchSnapshot();
    });

    it('renders outline variant snapshot', () => {
        const wrapper = mountWithPlugins(AppButton, {
            props: { variant: 'outline' },
            slots: { default: 'Outline Button' },
        });

        expect(wrapper.html()).toMatchSnapshot();
    });

    it('renders large size snapshot', () => {
        const wrapper = mountWithPlugins(AppButton, {
            props: { size: 'lg' },
            slots: { default: 'Large Button' },
        });

        expect(wrapper.html()).toMatchSnapshot();
    });

    it('renders icon size snapshot', () => {
        const wrapper = mountWithPlugins(AppButton, {
            props: { size: 'icon' },
            slots: { default: '🚀' },
        });

        expect(wrapper.html()).toMatchSnapshot();
    });

    it('renders disabled button snapshot', () => {
        const wrapper = mountWithPlugins(AppButton, {
            props: { disabled: true },
            slots: { default: 'Disabled Button' },
        });

        expect(wrapper.html()).toMatchSnapshot();
    });

    it('renders button as link snapshot', () => {
        const wrapper = mountWithPlugins(AppButton, {
            props: { as: 'a', href: 'https://example.com' },
            slots: { default: 'Link Button' },
        });

        expect(wrapper.html()).toMatchSnapshot();
    });

    it('renders button with custom class snapshot', () => {
        const wrapper = mountWithPlugins(AppButton, {
            props: { class: 'custom-button-class' },
            slots: { default: 'Custom Button' },
        });

        expect(wrapper.html()).toMatchSnapshot();
    });

    it('renders all variants snapshot', () => {
        const variants = [
            'default',
            'destructive',
            'outline',
            'secondary',
            'ghost',
            'link',
        ] as const;

        variants.forEach(variant => {
            const wrapper = mountWithPlugins(AppButton, {
                props: { variant },
                slots: { default: `${variant} button` },
            });

            expect(wrapper.html()).toMatchSnapshot(`button-variant-${variant}`);
        });
    });

    it('renders all sizes snapshot', () => {
        const sizes = ['xs', 'sm', 'default', 'lg', 'icon'] as const;

        sizes.forEach(size => {
            const wrapper = mountWithPlugins(AppButton, {
                props: { size },
                slots: { default: `${size} button` },
            });

            expect(wrapper.html()).toMatchSnapshot(`button-size-${size}`);
        });
    });
});
