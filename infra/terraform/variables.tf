variable "project_id" {
  description = "GCP Project ID"
  default     = "mini-ecommerce-project-481416"
}

variable "region" {
  description = "GCP Region"
  default     = "us-central1"
}

variable "db_password" {
  description = "MySQL database password"
  sensitive   = true
  default     = "ShopNex@2026"
}
