"use strict";
var __createBinding = (this && this.__createBinding) || (Object.create ? (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    var desc = Object.getOwnPropertyDescriptor(m, k);
    if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
      desc = { enumerable: true, get: function() { return m[k]; } };
    }
    Object.defineProperty(o, k2, desc);
}) : (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    o[k2] = m[k];
}));
var __exportStar = (this && this.__exportStar) || function(m, exports) {
    for (var p in m) if (p !== "default" && !Object.prototype.hasOwnProperty.call(exports, p)) __createBinding(exports, m, p);
};
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const ARRAPPEND_1 = __importDefault(require("./ARRAPPEND"));
const ARRINDEX_1 = __importDefault(require("./ARRINDEX"));
const ARRINSERT_1 = __importDefault(require("./ARRINSERT"));
const ARRLEN_1 = __importDefault(require("./ARRLEN"));
const ARRPOP_1 = __importDefault(require("./ARRPOP"));
const ARRTRIM_1 = __importDefault(require("./ARRTRIM"));
const CLEAR_1 = __importDefault(require("./CLEAR"));
const DEBUG_MEMORY_1 = __importDefault(require("./DEBUG_MEMORY"));
const DEL_1 = __importDefault(require("./DEL"));
const FORGET_1 = __importDefault(require("./FORGET"));
const GET_1 = __importDefault(require("./GET"));
const MERGE_1 = __importDefault(require("./MERGE"));
const MGET_1 = __importDefault(require("./MGET"));
const MSET_1 = __importDefault(require("./MSET"));
const NUMINCRBY_1 = __importDefault(require("./NUMINCRBY"));
const NUMMULTBY_1 = __importDefault(require("./NUMMULTBY"));
const OBJKEYS_1 = __importDefault(require("./OBJKEYS"));
const OBJLEN_1 = __importDefault(require("./OBJLEN"));
// import RESP from './RESP';
const SET_1 = __importDefault(require("./SET"));
const STRAPPEND_1 = __importDefault(require("./STRAPPEND"));
const STRLEN_1 = __importDefault(require("./STRLEN"));
const TOGGLE_1 = __importDefault(require("./TOGGLE"));
const TYPE_1 = __importDefault(require("./TYPE"));
__exportStar(require("./helpers"), exports);
exports.default = {
    ARRAPPEND: ARRAPPEND_1.default,
    arrAppend: ARRAPPEND_1.default,
    ARRINDEX: ARRINDEX_1.default,
    arrIndex: ARRINDEX_1.default,
    ARRINSERT: ARRINSERT_1.default,
    arrInsert: ARRINSERT_1.default,
    ARRLEN: ARRLEN_1.default,
    arrLen: ARRLEN_1.default,
    ARRPOP: ARRPOP_1.default,
    arrPop: ARRPOP_1.default,
    ARRTRIM: ARRTRIM_1.default,
    arrTrim: ARRTRIM_1.default,
    CLEAR: CLEAR_1.default,
    clear: CLEAR_1.default,
    DEBUG_MEMORY: DEBUG_MEMORY_1.default,
    debugMemory: DEBUG_MEMORY_1.default,
    DEL: DEL_1.default,
    del: DEL_1.default,
    FORGET: FORGET_1.default,
    forget: FORGET_1.default,
    GET: GET_1.default,
    get: GET_1.default,
    MERGE: MERGE_1.default,
    merge: MERGE_1.default,
    MGET: MGET_1.default,
    mGet: MGET_1.default,
    MSET: MSET_1.default,
    mSet: MSET_1.default,
    NUMINCRBY: NUMINCRBY_1.default,
    numIncrBy: NUMINCRBY_1.default,
    /**
     * @deprecated since JSON version 2.0
     */
    NUMMULTBY: NUMMULTBY_1.default,
    /**
     * @deprecated since JSON version 2.0
     */
    numMultBy: NUMMULTBY_1.default,
    OBJKEYS: OBJKEYS_1.default,
    objKeys: OBJKEYS_1.default,
    OBJLEN: OBJLEN_1.default,
    objLen: OBJLEN_1.default,
    // RESP,
    // resp: RESP,
    SET: SET_1.default,
    set: SET_1.default,
    STRAPPEND: STRAPPEND_1.default,
    strAppend: STRAPPEND_1.default,
    STRLEN: STRLEN_1.default,
    strLen: STRLEN_1.default,
    TOGGLE: TOGGLE_1.default,
    toggle: TOGGLE_1.default,
    TYPE: TYPE_1.default,
    type: TYPE_1.default
};
//# sourceMappingURL=index.js.map