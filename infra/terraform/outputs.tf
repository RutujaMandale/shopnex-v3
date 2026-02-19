output "cloud_run_url" {
  description = "Live URL of the ShopNex app"
  value       = google_cloud_run_v2_service.shopnex.uri
}

output "artifact_registry" {
  description = "Artifact Registry repository"
  value       = google_artifact_registry_repository.shopnex.name
}

output "cloud_sql_connection" {
  description = "Cloud SQL connection name"
  value       = google_sql_database_instance.shopnex.connection_name
}
