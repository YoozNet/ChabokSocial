export * from './helpers';
declare const _default: {
    ARRAPPEND: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, path: import("@redis/client/dist/lib/RESP/types").RedisArgument, json: import("./helpers").RedisJSON, ...jsons: import("./helpers").RedisJSON[]) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NumberReply<number> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").NumberReply<number>>;
    };
    arrAppend: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, path: import("@redis/client/dist/lib/RESP/types").RedisArgument, json: import("./helpers").RedisJSON, ...jsons: import("./helpers").RedisJSON[]) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NumberReply<number> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").NumberReply<number>>;
    };
    ARRINDEX: {
        readonly IS_READ_ONLY: true;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, path: import("@redis/client/dist/lib/RESP/types").RedisArgument, json: import("./helpers").RedisJSON, options?: import("./ARRINDEX").JsonArrIndexOptions | undefined) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NumberReply<number> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").NumberReply<number>>;
    };
    arrIndex: {
        readonly IS_READ_ONLY: true;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, path: import("@redis/client/dist/lib/RESP/types").RedisArgument, json: import("./helpers").RedisJSON, options?: import("./ARRINDEX").JsonArrIndexOptions | undefined) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NumberReply<number> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").NumberReply<number>>;
    };
    ARRINSERT: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, path: import("@redis/client/dist/lib/RESP/types").RedisArgument, index: number, json: import("./helpers").RedisJSON, ...jsons: import("./helpers").RedisJSON[]) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NumberReply<number> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").NumberReply<number>>;
    };
    arrInsert: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, path: import("@redis/client/dist/lib/RESP/types").RedisArgument, index: number, json: import("./helpers").RedisJSON, ...jsons: import("./helpers").RedisJSON[]) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NumberReply<number> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").NumberReply<number>>;
    };
    ARRLEN: {
        readonly IS_READ_ONLY: true;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, options?: import("./ARRLEN").JsonArrLenOptions | undefined) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NumberReply<number> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").NumberReply<number>>;
    };
    arrLen: {
        readonly IS_READ_ONLY: true;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, options?: import("./ARRLEN").JsonArrLenOptions | undefined) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NumberReply<number> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").NumberReply<number>>;
    };
    ARRPOP: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, options?: import("./ARRPOP").RedisArrPopOptions | undefined) => void;
        readonly transformReply: (this: void, reply: import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").BlobStringReply<string> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").BlobStringReply<string>>) => string | number | boolean | import("@redis/client/dist/lib/RESP/types").NullReply | Date | {
            [key: string]: import("./helpers").RedisJSON;
            [key: number]: import("./helpers").RedisJSON;
        } | (import("@redis/client/dist/lib/RESP/types").NullReply | import("./helpers").RedisJSON)[] | null;
    };
    arrPop: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, options?: import("./ARRPOP").RedisArrPopOptions | undefined) => void;
        readonly transformReply: (this: void, reply: import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").BlobStringReply<string> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").BlobStringReply<string>>) => string | number | boolean | import("@redis/client/dist/lib/RESP/types").NullReply | Date | {
            [key: string]: import("./helpers").RedisJSON;
            [key: number]: import("./helpers").RedisJSON;
        } | (import("@redis/client/dist/lib/RESP/types").NullReply | import("./helpers").RedisJSON)[] | null;
    };
    ARRTRIM: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, path: import("@redis/client/dist/lib/RESP/types").RedisArgument, start: number, stop: number) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NumberReply<number> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").NumberReply<number>>;
    };
    arrTrim: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, path: import("@redis/client/dist/lib/RESP/types").RedisArgument, start: number, stop: number) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NumberReply<number> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").NumberReply<number>>;
    };
    CLEAR: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, options?: import("./CLEAR").JsonClearOptions | undefined) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NumberReply<number>;
    };
    clear: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, options?: import("./CLEAR").JsonClearOptions | undefined) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NumberReply<number>;
    };
    DEBUG_MEMORY: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, options?: import("./DEBUG_MEMORY").JsonDebugMemoryOptions | undefined) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NumberReply<number>;
    };
    debugMemory: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, options?: import("./DEBUG_MEMORY").JsonDebugMemoryOptions | undefined) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NumberReply<number>;
    };
    DEL: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, options?: import("./DEL").JsonDelOptions | undefined) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NumberReply<number>;
    };
    del: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, options?: import("./DEL").JsonDelOptions | undefined) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NumberReply<number>;
    };
    FORGET: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, options?: import("./FORGET").JsonForgetOptions | undefined) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NumberReply<number>;
    };
    forget: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, options?: import("./FORGET").JsonForgetOptions | undefined) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NumberReply<number>;
    };
    GET: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, options?: import("./GET").JsonGetOptions | undefined) => void;
        readonly transformReply: typeof import("./helpers").transformRedisJsonNullReply;
    };
    get: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, options?: import("./GET").JsonGetOptions | undefined) => void;
        readonly transformReply: typeof import("./helpers").transformRedisJsonNullReply;
    };
    MERGE: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, path: import("@redis/client/dist/lib/RESP/types").RedisArgument, value: import("./helpers").RedisJSON) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").SimpleStringReply<"OK">;
    };
    merge: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, path: import("@redis/client/dist/lib/RESP/types").RedisArgument, value: import("./helpers").RedisJSON) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").SimpleStringReply<"OK">;
    };
    MGET: {
        readonly IS_READ_ONLY: true;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, keys: import("@redis/client/dist/lib/RESP/types").RedisArgument[], path: import("@redis/client/dist/lib/RESP/types").RedisArgument) => void;
        readonly transformReply: (this: void, reply: (import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").BlobStringReply<string>)[]) => (import("@redis/client/dist/lib/RESP/types").NullReply | import("./helpers").RedisJSON)[];
    };
    mGet: {
        readonly IS_READ_ONLY: true;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, keys: import("@redis/client/dist/lib/RESP/types").RedisArgument[], path: import("@redis/client/dist/lib/RESP/types").RedisArgument) => void;
        readonly transformReply: (this: void, reply: (import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").BlobStringReply<string>)[]) => (import("@redis/client/dist/lib/RESP/types").NullReply | import("./helpers").RedisJSON)[];
    };
    MSET: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, items: import("./MSET").JsonMSetItem[]) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").SimpleStringReply<"OK">;
    };
    mSet: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, items: import("./MSET").JsonMSetItem[]) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").SimpleStringReply<"OK">;
    };
    NUMINCRBY: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, path: import("@redis/client/dist/lib/RESP/types").RedisArgument, by: number) => void;
        readonly transformReply: {
            readonly 2: (reply: import("@redis/client/dist/lib/RESP/types").UnwrapReply<import("@redis/client/dist/lib/RESP/types").BlobStringReply<string>>) => number | (number | null)[];
            readonly 3: () => import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").NumberReply<number> | import("@redis/client/dist/lib/RESP/types").DoubleReply<number>>;
        };
    };
    numIncrBy: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, path: import("@redis/client/dist/lib/RESP/types").RedisArgument, by: number) => void;
        readonly transformReply: {
            readonly 2: (reply: import("@redis/client/dist/lib/RESP/types").UnwrapReply<import("@redis/client/dist/lib/RESP/types").BlobStringReply<string>>) => number | (number | null)[];
            readonly 3: () => import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").NumberReply<number> | import("@redis/client/dist/lib/RESP/types").DoubleReply<number>>;
        };
    };
    /**
     * @deprecated since JSON version 2.0
     */
    NUMMULTBY: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, path: import("@redis/client/dist/lib/RESP/types").RedisArgument, by: number) => void;
        readonly transformReply: {
            readonly 2: (reply: import("@redis/client/dist/lib/RESP/types").UnwrapReply<import("@redis/client/dist/lib/RESP/types").BlobStringReply<string>>) => number | (number | null)[];
            readonly 3: () => import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").NumberReply<number> | import("@redis/client/dist/lib/RESP/types").DoubleReply<number>>;
        };
    };
    /**
     * @deprecated since JSON version 2.0
     */
    numMultBy: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, path: import("@redis/client/dist/lib/RESP/types").RedisArgument, by: number) => void;
        readonly transformReply: {
            readonly 2: (reply: import("@redis/client/dist/lib/RESP/types").UnwrapReply<import("@redis/client/dist/lib/RESP/types").BlobStringReply<string>>) => number | (number | null)[];
            readonly 3: () => import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").NumberReply<number> | import("@redis/client/dist/lib/RESP/types").DoubleReply<number>>;
        };
    };
    OBJKEYS: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, options?: import("./OBJKEYS").JsonObjKeysOptions | undefined) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").BlobStringReply<string>> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").BlobStringReply<string>>>;
    };
    objKeys: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, options?: import("./OBJKEYS").JsonObjKeysOptions | undefined) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").BlobStringReply<string>> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").BlobStringReply<string>>>;
    };
    OBJLEN: {
        readonly IS_READ_ONLY: true;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, options?: import("./OBJLEN").JsonObjLenOptions | undefined) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NumberReply<number> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").NumberReply<number>>;
    };
    objLen: {
        readonly IS_READ_ONLY: true;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, options?: import("./OBJLEN").JsonObjLenOptions | undefined) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NumberReply<number> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").NumberReply<number>>;
    };
    SET: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, path: import("@redis/client/dist/lib/RESP/types").RedisArgument, json: import("./helpers").RedisJSON, options?: import("./SET").JsonSetOptions | undefined) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").SimpleStringReply<"OK">;
    };
    set: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, path: import("@redis/client/dist/lib/RESP/types").RedisArgument, json: import("./helpers").RedisJSON, options?: import("./SET").JsonSetOptions | undefined) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").SimpleStringReply<"OK">;
    };
    STRAPPEND: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, append: string, options?: import("./STRAPPEND").JsonStrAppendOptions | undefined) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NumberReply<number> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").NumberReply<number>>;
    };
    strAppend: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, append: string, options?: import("./STRAPPEND").JsonStrAppendOptions | undefined) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NumberReply<number> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").NumberReply<number>>;
    };
    STRLEN: {
        readonly IS_READ_ONLY: true;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, options?: import("./STRLEN").JsonStrLenOptions | undefined) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NumberReply<number> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").NumberReply<number>>;
    };
    strLen: {
        readonly IS_READ_ONLY: true;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, options?: import("./STRLEN").JsonStrLenOptions | undefined) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NumberReply<number> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").NumberReply<number>>;
    };
    TOGGLE: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, path: import("@redis/client/dist/lib/RESP/types").RedisArgument) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").NumberReply<number> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").NumberReply<number>>;
    };
    toggle: {
        readonly IS_READ_ONLY: false;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, path: import("@redis/client/dist/lib/RESP/types").RedisArgument) => void;
        readonly transformReply: () => import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").NumberReply<number> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").NumberReply<number>>;
    };
    TYPE: {
        readonly IS_READ_ONLY: true;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, options?: import("./TYPE").JsonTypeOptions | undefined) => void;
        readonly transformReply: {
            readonly 2: () => import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").BlobStringReply<string> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").BlobStringReply<string>>;
            readonly 3: (reply: (import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").BlobStringReply<string> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").BlobStringReply<string>>)[]) => import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").BlobStringReply<string> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").BlobStringReply<string>>;
        };
    };
    type: {
        readonly IS_READ_ONLY: true;
        readonly parseCommand: (this: void, parser: import("@redis/client/dist/lib/client/parser").CommandParser, key: import("@redis/client/dist/lib/RESP/types").RedisArgument, options?: import("./TYPE").JsonTypeOptions | undefined) => void;
        readonly transformReply: {
            readonly 2: () => import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").BlobStringReply<string> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").BlobStringReply<string>>;
            readonly 3: (reply: (import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").BlobStringReply<string> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").BlobStringReply<string>>)[]) => import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").BlobStringReply<string> | import("@redis/client/dist/lib/RESP/types").ArrayReply<import("@redis/client/dist/lib/RESP/types").NullReply | import("@redis/client/dist/lib/RESP/types").BlobStringReply<string>>;
        };
    };
};
export default _default;
//# sourceMappingURL=index.d.ts.map