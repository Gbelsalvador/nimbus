import '@testing-library/jest-dom';
import { setActivePinia } from 'pinia';
import { beforeEach, vi } from 'vitest';

import { createTestingPinia } from '@pinia/testing';

beforeEach(() => {
    // Reset Pinia stores before each test
    setActivePinia(createTestingPinia({ stubActions: false }));

    // Clear all mocks
    vi.clearAllMocks();
});

// Mock console methods to reduce noise in tests
global.console = {
    ...console,
    warn: vi.fn(),
    error: vi.fn(),
};
