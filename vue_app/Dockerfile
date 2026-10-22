# syntax=docker/dockerfile:1
FROM node:24.22.1-alpine AS build-stage
WORKDIR /app

RUN npm install --global pnpm@11.20.0

COPY package.json pnpm-lock.yaml pnpm-workspace.yaml ./
RUN --mount=type=cache,id=downstream-web-pnpm,target=/pnpm/store \
  pnpm install --frozen-lockfile --store-dir /pnpm/store

COPY . .
RUN pnpm build

# production stage
FROM nginx:stable-alpine AS production-stage

COPY config/nginx-vue.conf /etc/nginx/nginx.conf
COPY --from=build-stage /app/dist /usr/share/nginx/html

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
