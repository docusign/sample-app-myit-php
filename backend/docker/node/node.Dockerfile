FROM --platform=linux/amd64 node:16-buster-slim

WORKDIR /var/www/html

COPY ./sockets/.env.example /var/www/html/.env

COPY ./sockets /var/www/html

RUN npm install

EXPOSE 3001

CMD ["npm", "start"]