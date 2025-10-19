# Base (App) Components

This directory contains foundational, reusable UI components that serve as the visual and interactive building blocks for the application. These components are intentionally style-focused and agnostic of business logic, making them suitable for use across many domains and features.

## Overview

- **Purpose:** Provide a consistent, maintainable set of UI primitives for rapid development.
- **Scope:** Includes buttons, inputs, modals, tabs, and other common interface elements.
- **Design:** Most components are based on [shadcn-vue](https://ui.shadcn.com/docs/components/vue), adapted for this project’s conventions and styling.

## Characteristics

- Style-driven, not business-aware
- Stateless or simple internal state only
- Configurable via props and slots
- Used throughout the application for consistency

## Examples

- `AppButton.vue`
- `AppInput.vue`
- `AppModal.vue`
- `AppTabs.vue`
- `AppSonner.vue`
- `AppSidebarProvider.vue`

## shadcn-vue Integration

Most components in this directory are wrappers or adaptations of [shadcn-vue](https://www.shadcn-vue.com/docs/components) components. This ensures a modern, accessible, and themeable UI foundation while allowing for project-specific customization.

## Adding a New Base Component

1. **Install the shadcn-vue Component (if applicable):**
    - Use the shadcn-vue CLI or follow their documentation to install the desired component.
    - Adapt the component as needed to fit project conventions (e.g., prefix with `App`, adjust props, or styling).

2. **Export the Component:**
    - Open `index.ts` in this directory.
    - Export the new component with the `App` prefix for consistency.
    - Example:
        ```ts
        export { default as AppButton } from './AppButton.vue';
        export { default as AppNewComponent } from './AppNewComponent.vue';
        ```

3. **Usage:**
    - Import the component using its `App`-prefixed name in other parts of the application.

This approach ensures all base components are discoverable, consistently named, and easy to maintain.
