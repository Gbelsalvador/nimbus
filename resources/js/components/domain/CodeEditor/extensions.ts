import { jsonLinter } from '@/components/domain/CodeEditor/jsonLinter';
import { JsonSchema } from '@/interfaces/schema/shape';
import { json } from '@codemirror/lang-json';
import { lintGutter } from '@codemirror/lint';
import { EditorState } from '@codemirror/state';
import { jsonSchema } from 'codemirror-json-schema';

export const jsonExtensions = (readonly: boolean, schema: JsonSchema | undefined) => {
    const extensions = commonExtensions(readonly);

    extensions.push(json());

    if (schema !== undefined) {
        extensions.push(jsonSchema(schema));
    }

    if (!readonly) {
        extensions.push(jsonLinter);
    }

    return extensions;
};

export const commonExtensions = (readonly: boolean) => {
    const extensions = [lintGutter()];

    if (readonly) {
        extensions.push(EditorState.readOnly.of(true));
    }

    return extensions;
};

export const fallbackExtensions = (readonly: boolean) => commonExtensions(readonly);
