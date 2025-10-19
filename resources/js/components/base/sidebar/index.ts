import type { VariantProps } from 'class-variance-authority';
import { cva } from 'class-variance-authority';
import type { HTMLAttributes } from 'vue';

export interface SidebarProps {
    side?: 'left' | 'right';
    variant?: 'sidebar' | 'floating' | 'inset';
    collapsible?: 'offcanvas' | 'icon' | 'none';
    class?: HTMLAttributes['class'];
}

export { default as AppSidebar } from './AppSidebar.vue';
export { default as AppSidebarContent } from './AppSidebarContent.vue';
export { default as AppSidebarFooter } from './AppSidebarFooter.vue';
export { default as AppSidebarGroup } from './AppSidebarGroup.vue';
export { default as AppSidebarGroupAction } from './AppSidebarGroupAction.vue';
export { default as AppSidebarGroupContent } from './AppSidebarGroupContent.vue';
export { default as AppSidebarGroupLabel } from './AppSidebarGroupLabel.vue';
export { default as AppSidebarHeader } from './AppSidebarHeader.vue';
export { default as AppSidebarInput } from './AppSidebarInput.vue';
export { default as AppSidebarInset } from './AppSidebarInset.vue';
export { default as AppSidebarMenu } from './AppSidebarMenu.vue';
export { default as AppSidebarMenuAction } from './AppSidebarMenuAction.vue';
export { default as AppSidebarMenuBadge } from './AppSidebarMenuBadge.vue';
export { default as AppSidebarMenuButton } from './AppSidebarMenuButton.vue';
export { default as AppSidebarMenuItem } from './AppSidebarMenuItem.vue';
export { default as AppSidebarMenuSkeleton } from './AppSidebarMenuSkeleton.vue';
export { default as AppSidebarMenuSub } from './AppSidebarMenuSub.vue';
export { default as AppSidebarMenuSubButton } from './AppSidebarMenuSubButton.vue';
export { default as AppSidebarMenuSubItem } from './AppSidebarMenuSubItem.vue';
export { default as AppSidebarProvider } from './AppSidebarProvider.vue';
export { default as AppSidebarRail } from './AppSidebarRail.vue';
export { default as AppSidebarSeparator } from './AppSidebarSeparator.vue';
export { default as AppSidebarTrigger } from './AppSidebarTrigger.vue';

export { useSidebar } from './utils';

export const sidebarMenuButtonVariants = cva(
    'relative peer/menu-button flex w-full items-center gap-2 rounded-md p-2 text-left text-sm outline-none ring-sidebar-ring transition-[width,height,padding] focus-visible:ring-2 active:bg-sidebar-accent active:text-sidebar-accent-foreground disabled:pointer-events-none disabled:opacity-50 group-has-[[data-sidebar=menu-action]]/menu-item:pr-8 aria-disabled:pointer-events-none aria-disabled:opacity-50 data-[active=true]:font-medium  data-[state=open]:hover:bg-sidebar-accent data-[state=open]:hover:text-sidebar-accent-foreground group-data-[collapsible=icon]:!size-8 group-data-[collapsible=icon]:!p-2 [&>span:last-child]:truncate [&>svg]:size-4 [&>svg]:shrink-0',
    {
        variants: {
            variant: {
                default:
                    'hover:bg-sidebar-accent hover:text-sidebar-accent-foreground data-[active=true]:bg-sidebar-accent data-[active=true]:text-sidebar-accent-foreground',
                outline:
                    'bg-white shadow-[0_0_0_1px_hsl(var(--sidebar-border))] hover:bg-sidebar-accent hover:text-sidebar-accent-foreground hover:shadow-[0_0_0_1px_hsl(var(--sidebar-accent))] dark:bg-zinc-950  data-[active=true]:bg-sidebar-accent data-[active=true]:text-sidebar-accent-foreground',
            },
            size: {
                default: 'h-8 text-sm',
                sm: 'h-7 text-xs',
                lg: 'h-12 text-sm group-data-[collapsible=icon]:!p-0',
            },
        },
        defaultVariants: {
            variant: 'default',
            size: 'default',
        },
    },
);

export type SidebarMenuButtonVariants = VariantProps<typeof sidebarMenuButtonVariants>;
