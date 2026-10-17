FROM node:22-alpine AS build-stage
COPY . .

RUN yarn install --frozen-lockfile
RUN yarn build

# production stage
FROM nginx:stable-alpine AS production-stage

# Copy nginx
COPY config/nginx-vue.conf /etc/nginx/nginx.conf
COPY --from=build-stage /dist /usr/share/nginx/html

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
