provider "google" {
  project = var.project_id
  region  = var.region
}

# Artifact Registry
resource "google_artifact_registry_repository" "shopnex" {
  location      = var.region
  repository_id = "shopnex-repo"
  format        = "DOCKER"
}

# Secret Manager Secrets
resource "google_secret_manager_secret" "stripe_key" {
  secret_id = "stripe-secret-key"
  replication { auto {} }
}

resource "google_secret_manager_secret" "admin_username" {
  secret_id = "admin-username"
  replication { auto {} }
}

resource "google_secret_manager_secret" "admin_password" {
  secret_id = "admin-password"
  replication { auto {} }
}

resource "google_secret_manager_secret" "jwt_secret" {
  secret_id = "jwt-secret"
  replication { auto {} }
}

resource "google_secret_manager_secret" "db_host" {
  secret_id = "db-host"
  replication { auto {} }
}

resource "google_secret_manager_secret" "db_user" {
  secret_id = "db-user"
  replication { auto {} }
}

resource "google_secret_manager_secret" "db_pass" {
  secret_id = "db-pass"
  replication { auto {} }
}

resource "google_secret_manager_secret" "db_name" {
  secret_id = "db-name"
  replication { auto {} }
}

# Cloud SQL (MySQL)
resource "google_sql_database_instance" "shopnex" {
  name             = "shopnex-mysql"
  database_version = "MYSQL_8_0"
  region           = var.region

  settings {
    tier = "db-f1-micro"
    backup_configuration { enabled = true }
    ip_configuration { ipv4_enabled = true }
  }

  deletion_protection = false
}

resource "google_sql_database" "shopnex_db" {
  name     = "shopnex_db"
  instance = google_sql_database_instance.shopnex.name
}

resource "google_sql_user" "shopnex_user" {
  name     = "shopnex_user"
  instance = google_sql_database_instance.shopnex.name
  password = var.db_password
}

# Service Account for Cloud Run
resource "google_service_account" "shopnex_sa" {
  account_id   = "shopnex-cloudrun-sa"
  display_name = "ShopNex Cloud Run SA"
}

resource "google_project_iam_member" "secret_accessor" {
  project = var.project_id
  role    = "roles/secretmanager.secretAccessor"
  member  = "serviceAccount:${google_service_account.shopnex_sa.email}"
}

resource "google_project_iam_member" "cloudsql_client" {
  project = var.project_id
  role    = "roles/cloudsql.client"
  member  = "serviceAccount:${google_service_account.shopnex_sa.email}"
}

# Cloud Run Service
resource "google_cloud_run_v2_service" "shopnex" {
  name     = "shopnex"
  location = var.region

  template {
    service_account = google_service_account.shopnex_sa.email

    containers {
      image = "us-central1-docker.pkg.dev/${var.project_id}/shopnex-repo/shopnex:latest"
      ports { container_port = 8080 }

      env { name = "STRIPE_SECRET_KEY"
        value_source { secret_key_ref { secret = google_secret_manager_secret.stripe_key.secret_id; version = "latest" } }
      }
      env { name = "ADMIN_USERNAME"
        value_source { secret_key_ref { secret = google_secret_manager_secret.admin_username.secret_id; version = "latest" } }
      }
      env { name = "ADMIN_PASSWORD"
        value_source { secret_key_ref { secret = google_secret_manager_secret.admin_password.secret_id; version = "latest" } }
      }
      env { name = "JWT_SECRET"
        value_source { secret_key_ref { secret = google_secret_manager_secret.jwt_secret.secret_id; version = "latest" } }
      }
      env { name = "DB_HOST"
        value_source { secret_key_ref { secret = google_secret_manager_secret.db_host.secret_id; version = "latest" } }
      }
      env { name = "DB_USER"
        value_source { secret_key_ref { secret = google_secret_manager_secret.db_user.secret_id; version = "latest" } }
      }
      env { name = "DB_PASS"
        value_source { secret_key_ref { secret = google_secret_manager_secret.db_pass.secret_id; version = "latest" } }
      }
      env { name = "DB_NAME"
        value_source { secret_key_ref { secret = google_secret_manager_secret.db_name.secret_id; version = "latest" } }
      }
    }
  }
}

resource "google_cloud_run_v2_service_iam_member" "public_access" {
  location = google_cloud_run_v2_service.shopnex.location
  name     = google_cloud_run_v2_service.shopnex.name
  role     = "roles/run.invoker"
  member   = "allUsers"
}
