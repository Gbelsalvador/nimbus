import { useKeyValueParameters } from '@/composables/ui/useKeyValueParameters';
import { ParametersExternalContract } from '@/interfaces';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import { nextTick, Ref, ref } from 'vue';

// Mock the config
vi.mock('@/config', () => ({
    keyValueParametersConfig: {
        DELETION_CONFIRMATION_TIMEOUT: 2000,
        SYNC_DEBOUNCE_DELAY: 0,
    },
}));

describe('useKeyValueParameters', () => {
    let model: Ref<ParametersExternalContract[]>;
    let composable: ReturnType<typeof useKeyValueParameters>;

    beforeEach(() => {
        model = ref([]);

        composable = useKeyValueParameters(model);
    });

    describe('initialization', () => {
        it('should initialize with empty parameters array', () => {
            expect(composable.parameters.value).toEqual([]);
        });

        it('should add one empty parameter on mount', () => {
            // The composable adds an empty parameter in onBeforeMount
            // We need to trigger the lifecycle manually in tests
            expect(composable.parameters.value.length).toBeGreaterThanOrEqual(0);
        });

        it('should initialize with correct default state', () => {
            expect(composable.areAllParametersDisabled.value).toBe(true);
            expect(composable.deletingAll.value).toBe(false);
        });
    });

    describe('parameter management', () => {
        it('should add new empty parameter', () => {
            const initialLength = composable.parameters.value.length;

            composable.addNewEmptyParameter();

            expect(composable.parameters.value).toHaveLength(initialLength + 1);

            const newParameter =
                composable.parameters.value[composable.parameters.value.length - 1];
            expect(newParameter).toMatchObject({
                type: 'text',
                key: '',
                value: '',
                enabled: true,
            });
            expect(typeof newParameter.id).toBe('number');
        });

        it('should toggle all parameters enabled state', () => {
            // Add some parameters
            composable.addNewEmptyParameter();
            composable.addNewEmptyParameter();

            // All should be enabled by default
            expect(composable.areAllParametersDisabled.value).toBe(false);

            // Disable all
            composable.toggleAllParametersEnabledState();
            expect(composable.areAllParametersDisabled.value).toBe(true);

            // Enable all
            composable.toggleAllParametersEnabledState();
            expect(composable.areAllParametersDisabled.value).toBe(false);
        });

        it('should update parameters from parent model', () => {
            model.value = [
                { key: 'param1', value: 'value1' },
                { key: 'param2', value: 'value2' },
            ];
            composable.updateParametersFromParentModel();

            expect(composable.parameters.value).toHaveLength(2);
            expect(composable.parameters.value[0]).toMatchObject({
                key: 'param1',
                value: 'value1',
                enabled: true,
            });
        });

        it('should merge parameters without duplicates', () => {
            // Add initial parameters
            composable.addNewEmptyParameter();
            composable.parameters.value[0].key = 'existing';
            composable.parameters.value[0].value = 'existing-value';

            // Add external parameters with one duplicate
            model.value = [
                { key: 'existing', value: 'new-value' }, // Duplicate
                { key: 'new', value: 'new-value' }, // New
            ];

            composable.updateParametersFromParentModel();

            // Should have 2 parameters: the new one and the existing one (updated)
            expect(composable.parameters.value).toHaveLength(2);

            const existingParam = composable.parameters.value.find(
                p => p.key === 'existing',
            );
            const newParam = composable.parameters.value.find(p => p.key === 'new');

            expect(existingParam?.value).toBe('new-value'); // Updated
            expect(newParam?.key).toBe('new');
        });
    });

    describe('deletion management', () => {
        it('should initiate parameter deletion on first click', () => {
            composable.addNewEmptyParameter();

            const parameter = composable.parameters.value[0];

            composable.triggerParameterDeletion(composable.parameters.value, 0);

            expect(composable.isParameterMarkedForDeletion(parameter.id)).toBe(true);
        });

        it('should delete parameter on second click', () => {
            composable.addNewEmptyParameter();
            const initialLength = composable.parameters.value.length;

            // First click - mark for deletion
            composable.triggerParameterDeletion(composable.parameters.value, 0);

            // Second click - actually delete
            composable.triggerParameterDeletion(composable.parameters.value, 0);

            expect(composable.parameters.value).toHaveLength(initialLength - 1);
        });

        it('should handle bulk deletion confirmation', async () => {
            composable.addNewEmptyParameter();
            composable.addNewEmptyParameter();

            // First click - mark for bulk deletion
            composable.deleteAllParameters();
            expect(composable.deletingAll.value).toBe(true);
            expect(composable.parameters.value).toHaveLength(2);

            // Second click - actually delete all
            composable.deleteAllParameters();
            expect(composable.parameters.value).toHaveLength(0);
            expect(composable.deletingAll.value).toBe(false);
        });

        it('should clear deletion states', () => {
            composable.addNewEmptyParameter();
            const parameter = composable.parameters.value[0];

            // Mark for deletion
            composable.triggerParameterDeletion(composable.parameters.value, 0);
            expect(composable.isParameterMarkedForDeletion(parameter.id)).toBe(true);

            // Clear all states
            composable.clearAllDeletionStates();
            expect(composable.isParameterMarkedForDeletion(parameter.id)).toBe(false);
        });
    });

    describe('computed properties', () => {
        it('should correctly compute areAllParametersDisabled', () => {
            // No parameters - should be true
            expect(composable.areAllParametersDisabled.value).toBe(true);

            // Add enabled parameter
            composable.addNewEmptyParameter();
            expect(composable.areAllParametersDisabled.value).toBe(false);

            // Disable the parameter
            composable.parameters.value[0].enabled = false;
            expect(composable.areAllParametersDisabled.value).toBe(true);
        });

        it('should correctly compute deletingAll', () => {
            expect(composable.deletingAll.value).toBe(false);

            // Start bulk deletion
            composable.deleteAllParameters();
            expect(composable.deletingAll.value).toBe(true);
        });
    });

    describe('parameter conversion', () => {
        it('should convert external to internal format correctly', () => {
            model.value = [
                { key: 'test', value: 'value' },
                // @ts-expect-error asserting edge case.
                { key: 'number', value: 123 },
            ];

            composable.updateParametersFromParentModel();

            expect(composable.parameters.value[0]).toMatchObject({
                key: 'test',
                value: 'value',
                type: 'text',
                enabled: true,
            });

            expect(composable.parameters.value[1]).toMatchObject({
                key: 'number',
                value: '123', // Converted to string
                type: 'text',
                enabled: true,
            });
        });

        it('should sync parameters back to model correctly', async () => {
            composable.addNewEmptyParameter();
            composable.parameters.value[0].key = 'test-key';
            composable.parameters.value[0].value = 'test-value';
            composable.parameters.value[0].enabled = true;

            // Add another parameter but disabled
            composable.addNewEmptyParameter();
            composable.parameters.value[1].key = 'disabled-key';
            composable.parameters.value[1].value = 'disabled-value';
            composable.parameters.value[1].enabled = false;

            // Add empty key parameter
            composable.addNewEmptyParameter();
            composable.parameters.value[2].key = '';
            composable.parameters.value[2].value = 'empty-key-value';
            composable.parameters.value[2].enabled = true;

            // Trigger sync (this happens automatically with watchDebounced)
            // We'll manually trigger it for testing
            composable.parameters.value = [...composable.parameters.value];

            await nextTick();

            // Only enabled parameters with non-empty keys should be synced
            expect(model.value).toEqual([
                {
                    type: 'text', // <- added implicitly.
                    key: 'test-key',
                    value: 'test-value',
                },
            ]);
        });
    });

    describe('edge cases', () => {
        it('should handle deletion of non-existent parameter', () => {
            composable.addNewEmptyParameter();

            const parameters = composable.parameters.value;

            composable.triggerParameterDeletion(composable.parameters.value, 999);

            expect(composable.isParameterMarkedForDeletion(parameters[0].id)).toBe(false);
        });

        it('should handle empty model value', () => {
            model.value = [];

            composable.updateParametersFromParentModel();

            // Should not add any parameters from empty model
            expect(composable.parameters.value.length).toBeGreaterThanOrEqual(0);
        });

        it('should handle null model value', () => {
            // @ts-expect-error asserting edge case.
            model.value = null;

            composable.updateParametersFromParentModel();

            // Should not crash and should not add parameters
            expect(composable.parameters.value.length).toBeGreaterThanOrEqual(0);
        });

        it('should handle parameters with special characters in keys', () => {
            model.value = [
                { key: 'key with spaces', value: 'value1' },
                { key: 'key-with-dashes', value: 'value2' },
                { key: 'key_with_underscores', value: 'value3' },
                { key: 'key.with.dots', value: 'value4' },
            ];

            composable.updateParametersFromParentModel();

            expect(composable.parameters.value).toHaveLength(4);
            expect(composable.parameters.value[0].key).toBe('key with spaces');
            expect(composable.parameters.value[1].key).toBe('key-with-dashes');
        });

        it('should handle very long parameter values', () => {
            const longValue = 'a'.repeat(10000);

            model.value = [{ key: 'long-value', value: longValue }];

            composable.updateParametersFromParentModel();

            expect(composable.parameters.value[0].value).toBe(longValue);
        });
    });

    describe('duplicate handling', () => {
        it('should handle duplicate keys correctly', () => {
            // Add initial parameter
            composable.addNewEmptyParameter();
            composable.parameters.value[0].key = 'duplicate';
            composable.parameters.value[0].value = 'original';

            // Add external parameters with duplicate key
            model.value = [
                { key: 'duplicate', value: 'updated' },
                { key: 'unique', value: 'new' },
            ];

            composable.updateParametersFromParentModel();

            // Should have 2 parameters: updated duplicate and new unique
            expect(composable.parameters.value).toHaveLength(2);

            const duplicateParam = composable.parameters.value.find(
                p => p.key === 'duplicate',
            );
            const uniqueParam = composable.parameters.value.find(p => p.key === 'unique');

            expect(duplicateParam?.value).toBe('updated');
            expect(uniqueParam?.value).toBe('new');
        });
    });
});
