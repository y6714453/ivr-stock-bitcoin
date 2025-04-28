FROM php:8.2-cli

# התקנת curl ותמיכה ב-SSL
RUN apt-get update && apt-get install -y libcurl4-openssl-dev pkg-config libssl-dev && docker-php-ext-install curl

# הגדרות תקינות
RUN docker-php-ext-install mysqli

WORKDIR /var/www/html

COPY . .

CMD ["php", "-S", "0.0.0.0:10000"]
