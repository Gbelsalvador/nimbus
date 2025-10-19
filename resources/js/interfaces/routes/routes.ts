import { JsonSchema } from '@/interfaces/schema/shape';

export type RouteDefinition = {
    endpoint: string;
    method: string;
    schema: {
        shape: JsonSchema;
        extractionErrors: string | null;
    };
    shortEndpoint: string;
};

export interface RoutesGroup {
    /** The resource name that groups these routes */
    resource: string;

    /** Array of route definitions belonging to this group */
    routes: Array<RouteDefinition>;
}
