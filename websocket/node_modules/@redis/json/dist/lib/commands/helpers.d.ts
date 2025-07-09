import { BlobStringReply, NullReply } from "@redis/client/dist/lib/RESP/types";
export declare function transformRedisJsonNullReply(json: NullReply | BlobStringReply): NullReply | RedisJSON;
export type RedisJSON = null | boolean | number | string | Date | Array<RedisJSON> | {
    [key: string]: RedisJSON;
    [key: number]: RedisJSON;
};
export declare function transformRedisJsonArgument(json: RedisJSON): string;
export declare function transformRedisJsonReply(json: BlobStringReply): RedisJSON;
//# sourceMappingURL=helpers.d.ts.map