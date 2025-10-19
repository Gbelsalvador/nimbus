# Domain Components

This directory contains feature-specific components that handle the core business logic of the API client application.

These components are organized by domain and represent distinct functional areas within the app — from building API requests to viewing responses, managing environments, and more.

## Purpose

These components are tied to real-world usage scenarios of the app. They often use or compose:

- Base UI components (`components/base/`)
- Logic from composables (`composables/` or `functional/`)
- External libraries (e.g. for syntax highlighting)

> Domain components = "What the app does" in specific contexts.
