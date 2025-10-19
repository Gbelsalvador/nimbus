import { cva, type VariantProps } from 'class-variance-authority';

export { default as AppSheet } from './AppSheet.vue';
export { default as AppSheetClose } from './AppSheetClose.vue';
export { default as AppSheetContent } from './AppSheetContent.vue';
export { default as AppSheetDescription } from './AppSheetDescription.vue';
export { default as AppSheetFooter } from './AppSheetFooter.vue';
export { default as AppSheetHeader } from './AppSheetHeader.vue';
export { default as AppSheetTitle } from './AppSheetTitle.vue';
export { default as AppSheetTrigger } from './AppSheetTrigger.vue';

export const sheetVariants = cva(
    'fixed z-50 gap-4 bg-white p-6 shadow-lg transition ease-in-out data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:duration-300 data-[state=open]:duration-500 dark:bg-zinc-950',
    {
        variants: {
            side: {
                top: 'inset-x-0 top-0 border-b data-[state=closed]:slide-out-to-top data-[state=open]:slide-in-from-top',
                bottom: 'inset-x-0 bottom-0 border-t data-[state=closed]:slide-out-to-bottom data-[state=open]:slide-in-from-bottom',
                left: 'inset-y-0 left-0 h-full w-3/4 border-r data-[state=closed]:slide-out-to-left data-[state=open]:slide-in-from-left sm:max-w-sm',
                right: 'inset-y-0 right-0 h-full w-3/4 border-l data-[state=closed]:slide-out-to-right data-[state=open]:slide-in-from-right sm:max-w-sm',
            },
        },
        defaultVariants: {
            side: 'right',
        },
    },
);

export type SheetVariants = VariantProps<typeof sheetVariants>;
