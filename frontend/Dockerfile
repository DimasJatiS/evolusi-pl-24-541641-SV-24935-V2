# ── Stage 1: Build Frontend (Node.js) ──
FROM node:20-alpine AS builder

WORKDIR /app

# Copy dependency definition terlebih dahulu agar memanfaatkan cache layer
COPY package.json package-lock.json ./
RUN npm ci

# Copy source code dan compile produksi
COPY . .
RUN npm run build

# ── Stage 2: Runtime Production (Nginx Web Server) ──
FROM nginx:1.27-alpine AS runner

# Salin hasil build dari stage builder ke webroot Nginx
COPY --from=builder /app/dist /usr/share/nginx/html

# Buat konfigurasi Nginx agar mendukung routing Vue SPA (Vue Router HTML5 History)
RUN echo 'server { \
    listen 80; \
    server_name localhost; \
    location / { \
        root /usr/share/nginx/html; \
        index index.html index.htm; \
        try_files $uri $uri/ /index.html; \
    } \
}' > /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
