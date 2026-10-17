# syntax=docker/dockerfile:1
FROM node:22-alpine AS build-stage

COPY package.json yarn.lock ./
RUN --mount=type=cache,id=downstream-web-yarn,target=/tmp/yarn-cache \
  YARN_CACHE_FOLDER=/tmp/yarn-cache yarn install --frozen-lockfile

COPY . .
RUN yarn build

# production stage
FROM nginx:stable-alpine AS production-stage

# Copy nginx
COPY config/nginx-vue.conf /etc/nginx/nginx.conf
COPY --from=build-stage /dist /usr/share/nginx/html

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
