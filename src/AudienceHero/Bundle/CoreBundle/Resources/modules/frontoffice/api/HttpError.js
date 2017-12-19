/*
 * Taken from Admin-On-Rest.
 * Licensed under the MIT license.
 *
 * Copyright (c) 2016-present, Francois Zaninotto, Marmelab
 */
class HttpError extends Error {
    constructor(message, status, body = null) {
        super(message);
        this.message = message;
        this.status = status;
        this.body = body;
        this.name = this.constructor.name;
        if (typeof Error.captureStackTrace === 'function') {
            Error.captureStackTrace(this, this.constructor);
        } else {
            this.stack = new Error(message).stack;
        }
        this.stack = new Error().stack;
    }
}

export default HttpError;
