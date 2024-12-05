# Stage 1: Build Stage
FROM node:18.17.0 as build-stage

# Set working directory
WORKDIR /app

# Copy the project files into the container
COPY . /app

# Ensure the correct version of Yarn is installed, overwriting if necessary
RUN npm install -g yarn@1.22.22 --force

# Install dependencies and build the project
RUN yarn install
RUN yarn build

# Stage 2: Application Stage
FROM jimmyruan/radium-app:latest

# Copy the build artifacts from the build stage
COPY --from=build-stage /app/public /var/www/public
COPY --from=build-stage /app/resources /var/www/resources

# Copy other necessary source files, excluding the ones already copied
COPY --chown=www-data:www-data . /var/www

# Ensure storage and cache directories have the correct permissions
RUN rm -rf /var/www/storage/logs && mkdir -p /var/www/storage/logs /var/www/bootstrap/cache \
    && chmod -R 777 /var/www/storage /var/www/bootstrap/cache /var/www/storage/logs \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Command to start Laravel server
CMD ["php-fpm"]
