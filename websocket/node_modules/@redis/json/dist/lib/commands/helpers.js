"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.transformRedisJsonReply = exports.transformRedisJsonArgument = exports.transformRedisJsonNullReply = void 0;
const generic_transformers_1 = require("@redis/client/dist/lib/commands/generic-transformers");
function transformRedisJsonNullReply(json) {
    return (0, generic_transformers_1.isNullReply)(json) ? json : transformRedisJsonReply(json);
}
exports.transformRedisJsonNullReply = transformRedisJsonNullReply;
function transformRedisJsonArgument(json) {
    return JSON.stringify(json);
}
exports.transformRedisJsonArgument = transformRedisJsonArgument;
function transformRedisJsonReply(json) {
    const res = JSON.parse(json.toString());
    return res;
}
exports.transformRedisJsonReply = transformRedisJsonReply;
//# sourceMappingURL=helpers.js.map