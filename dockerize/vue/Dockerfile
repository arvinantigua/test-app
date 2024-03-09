FROM node:lts-alpine
WORKDIR /home/node/app
COPY . .
EXPOSE 8080
ENTRYPOINT npm install && npm run dev -- --host --port 8080