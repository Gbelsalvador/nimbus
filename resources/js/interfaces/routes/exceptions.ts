export type RouteExtractorException = {
    exception: ExceptionData;
    routeContext: ExceptionRouteContext;
    suggestedSolution?: string;
    ignoreData?: string;
};

export interface ExceptionRouteContext {
    uri?: string;
    methods?: string[];
    controllerClass?: string;
    controllerMethod?: string;
}

export interface ExceptionPrevious {
    message: string;
    file?: string;
    line?: number;
    trace?: string;
}

export interface ExceptionData {
    message: string;
    previous?: ExceptionPrevious | null;
}
