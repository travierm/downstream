FROM node:lts-alpine as build-stage
WORKDIR /vue_app

COPY /vue_app/package.json ./
COPY /vue_app/yarn.lock ./

RUN yarn install
COPY /vue_app .

RUN yarn build

# production stage
FROM nginx:stable-alpine as production-stage
COPY --from=build-stage /vue_app/dist /usr/share/nginx/html
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]