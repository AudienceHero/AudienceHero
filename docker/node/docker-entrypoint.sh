#!/bin/sh
set -e

yarn install
NODE_ENV=development yarn run encore dev --watch
