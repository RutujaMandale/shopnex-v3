# Security Implementation

## Security Controls

| Control | Implementation |
|---|---|
| HTTPS | Enforced automatically by Cloud Run |
| Secrets | All stored in GCP Secret Manager, none hardcoded |
| Authentication | JWT tokens (4hr expiry) for admin routes |
| SQL Injection | Parameterized queries (prepared statements) throughout |
| XSS Protection | htmlspecialchars() on all output |
| Input Validation | Server-side validation on all API endpoints |
| Rate Limiting | Cloud Run concurrency limits |
| IAM | Least-privilege service account |
| Container Security | Trivy scanning in CI pipeline |
| Dependency Scanning | PHP security audit in CI pipeline |
| No Hardcoded Creds | .gitignore excludes .env, all secrets via env vars |

## Secret Manager Secrets
- `stripe-secret-key` — Stripe API secret key
- `admin-username` — Admin dashboard username
- `admin-password` — Admin dashboard password
- `jwt-secret` — JWT signing key
- `db-host` — Cloud SQL host
- `db-user` — Database username
- `db-pass` — Database password
- `db-name` — Database name

## SQL Injection Protection
All database queries use prepared statements with bound parameters:
```php
$stmt = $db->prepare("SELECT * FROM products WHERE category = ?");
$stmt->bind_param('s', $category);
$stmt->execute();
```

## XSS Protection
All user-facing output is sanitized:
```php
echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8');
```
