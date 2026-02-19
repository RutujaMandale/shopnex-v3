# ShopNex — E-Commerce with DevSecOps on GCP

A secure 5-page e-commerce web application built with Core PHP, MySQL, and deployed on Google Cloud Run with a full DevSecOps pipeline.

## Tech Stack
- **Frontend:** Plain HTML5 + CSS3 + Vanilla JavaScript
- **Backend:** Core PHP 8.1 (no frameworks)
- **Database:** Cloud SQL (MySQL 8.0)
- **Payment:** Stripe Test Mode
- **Hosting:** Google Cloud Run
- **CI/CD:** GitHub Actions
- **IaC:** Terraform
- **Security:** Secret Manager, Trivy, IAM

## Pages
1. `/index.html` — Home page with featured products
2. `/products.html` — All products with category filter
3. `/product-detail.html` — Individual product detail
4. `/cart.html` — Cart and Stripe checkout
5. `/admin.html` — Admin login and orders dashboard

## Local Development

### Prerequisites
- PHP 8.1+
- MySQL 8.0+
- Docker

### Run Locally
```bash
# 1. Set up MySQL
mysql -u root -p < backend/models/schema.sql

# 2. Set environment variables
export DB_HOST=localhost
export DB_USER=shopnex_user
export DB_PASS=ShopNex@2026
export DB_NAME=shopnex_db
export STRIPE_SECRET_KEY=sk_test_your_key
export ADMIN_USERNAME=admin
export ADMIN_PASSWORD=Admin@2026
export JWT_SECRET=your_jwt_secret

# 3. Run PHP built-in server
php -S localhost:8080 -t . backend/app/index.php

# 4. Or use Docker
docker build -t shopnex .
docker run -p 8080:8080 shopnex
```

## Deployment
See full deployment guide in the project documentation.

## Admin Login
- URL: `/admin.html`
- Username: Set in Secret Manager (`admin-username`)
- Password: Set in Secret Manager (`admin-password`)

## Test Payment
Use Stripe test card: `4242 4242 4242 4242` with any future date and any CVV.
