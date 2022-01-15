/**
 * @fileoverview gRPC-Web generated client stub for private
 * @enhanceable
 * @public
 */

// GENERATED CODE -- DO NOT EDIT!


/* eslint-disable */
// @ts-nocheck



const grpc = {};
grpc.web = require('grpc-web');

const proto = {};
proto.private = require('./accounts_pb.js');

/**
 * @param {string} hostname
 * @param {?Object} credentials
 * @param {?grpc.web.ClientOptions} options
 * @constructor
 * @struct
 * @final
 */
proto.private.AccountServiceClient =
    function(hostname, credentials, options) {
  if (!options) options = {};
  options.format = 'text';

  /**
   * @private @const {!grpc.web.GrpcWebClientBase} The client
   */
  this.client_ = new grpc.web.GrpcWebClientBase(options);

  /**
   * @private @const {string} The hostname
   */
  this.hostname_ = hostname;

};


/**
 * @param {string} hostname
 * @param {?Object} credentials
 * @param {?grpc.web.ClientOptions} options
 * @constructor
 * @struct
 * @final
 */
proto.private.AccountServicePromiseClient =
    function(hostname, credentials, options) {
  if (!options) options = {};
  options.format = 'text';

  /**
   * @private @const {!grpc.web.GrpcWebClientBase} The client
   */
  this.client_ = new grpc.web.GrpcWebClientBase(options);

  /**
   * @private @const {string} The hostname
   */
  this.hostname_ = hostname;

};


/**
 * @const
 * @type {!grpc.web.MethodDescriptor<
 *   !proto.private.User,
 *   !proto.private.User>}
 */
const methodDescriptor_AccountService_Create = new grpc.web.MethodDescriptor(
  '/private.AccountService/Create',
  grpc.web.MethodType.UNARY,
  proto.private.User,
  proto.private.User,
  /**
   * @param {!proto.private.User} request
   * @return {!Uint8Array}
   */
  function(request) {
    return request.serializeBinary();
  },
  proto.private.User.deserializeBinary
);


/**
 * @param {!proto.private.User} request The
 *     request proto
 * @param {?Object<string, string>} metadata User defined
 *     call metadata
 * @param {function(?grpc.web.RpcError, ?proto.private.User)}
 *     callback The callback function(error, response)
 * @return {!grpc.web.ClientReadableStream<!proto.private.User>|undefined}
 *     The XHR Node Readable Stream
 */
proto.private.AccountServiceClient.prototype.create =
    function(request, metadata, callback) {
  return this.client_.rpcCall(this.hostname_ +
      '/private.AccountService/Create',
      request,
      metadata || {},
      methodDescriptor_AccountService_Create,
      callback);
};


/**
 * @param {!proto.private.User} request The
 *     request proto
 * @param {?Object<string, string>=} metadata User defined
 *     call metadata
 * @return {!Promise<!proto.private.User>}
 *     Promise that resolves to the response
 */
proto.private.AccountServicePromiseClient.prototype.create =
    function(request, metadata) {
  return this.client_.unaryCall(this.hostname_ +
      '/private.AccountService/Create',
      request,
      metadata || {},
      methodDescriptor_AccountService_Create);
};


/**
 * @const
 * @type {!grpc.web.MethodDescriptor<
 *   !proto.private.User,
 *   !proto.private.Account>}
 */
const methodDescriptor_AccountService_AuthenticateByEmailAndPassword = new grpc.web.MethodDescriptor(
  '/private.AccountService/AuthenticateByEmailAndPassword',
  grpc.web.MethodType.UNARY,
  proto.private.User,
  proto.private.Account,
  /**
   * @param {!proto.private.User} request
   * @return {!Uint8Array}
   */
  function(request) {
    return request.serializeBinary();
  },
  proto.private.Account.deserializeBinary
);


/**
 * @param {!proto.private.User} request The
 *     request proto
 * @param {?Object<string, string>} metadata User defined
 *     call metadata
 * @param {function(?grpc.web.RpcError, ?proto.private.Account)}
 *     callback The callback function(error, response)
 * @return {!grpc.web.ClientReadableStream<!proto.private.Account>|undefined}
 *     The XHR Node Readable Stream
 */
proto.private.AccountServiceClient.prototype.authenticateByEmailAndPassword =
    function(request, metadata, callback) {
  return this.client_.rpcCall(this.hostname_ +
      '/private.AccountService/AuthenticateByEmailAndPassword',
      request,
      metadata || {},
      methodDescriptor_AccountService_AuthenticateByEmailAndPassword,
      callback);
};


/**
 * @param {!proto.private.User} request The
 *     request proto
 * @param {?Object<string, string>=} metadata User defined
 *     call metadata
 * @return {!Promise<!proto.private.Account>}
 *     Promise that resolves to the response
 */
proto.private.AccountServicePromiseClient.prototype.authenticateByEmailAndPassword =
    function(request, metadata) {
  return this.client_.unaryCall(this.hostname_ +
      '/private.AccountService/AuthenticateByEmailAndPassword',
      request,
      metadata || {},
      methodDescriptor_AccountService_AuthenticateByEmailAndPassword);
};


module.exports = proto.private;

