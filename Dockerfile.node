FROM node:9-alpine

RUN apk add --no-cache \
		git \
		yarn

COPY docker/node/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint
WORKDIR /srv/audiencehero

ENTRYPOINT ["docker-entrypoint"]
