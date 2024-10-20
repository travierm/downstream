FROM node:16-alpine3.15 as build-stage
COPY . .

RUN yarn install
RUN yarn build

# production stage
FROM nginx:stable-alpine as production-stage

# Copy nginx
COPY config/nginx-vue.conf /etc/nginx/nginx.conf
COPY --from=build-stage /dist /usr/share/nginx/html

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]