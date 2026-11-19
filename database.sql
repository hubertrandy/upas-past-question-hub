CREATE DATABASE IF NOT EXISTS upsa_hub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE upsa_hub;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('student','admin') NOT NULL DEFAULT 'student',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS past_questions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  course_code VARCHAR(20) NOT NULL,
  course_title VARCHAR(150) NOT NULL,
  department VARCHAR(100) NOT NULL,
  semester VARCHAR(20) NOT NULL,
  academic_year VARCHAR(20) NOT NULL,
  paper_type VARCHAR(40) NOT NULL,
  file_name VARCHAR(255) NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  uploaded_by INT NOT NULL,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (course_code),
  FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Default admin: admin@upsa.edu.gh / password (change after first login)
INSERT INTO users (full_name, email, password, role)
VALUES ('System Admin', 'admin@upsa.edu.gh', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE email = email;
